const crypto = require('crypto');
const http = require('http');

const port = Number(process.env.WS_PORT || 3001);
const clients = new Set();

function frame(message) {
  const body = Buffer.from(message);
  const length = body.length;

  if (length < 126) {
    return Buffer.concat([Buffer.from([0x81, length]), body]);
  }

  if (length < 65536) {
    const header = Buffer.alloc(4);
    header[0] = 0x81;
    header[1] = 126;
    header.writeUInt16BE(length, 2);
    return Buffer.concat([header, body]);
  }

  const header = Buffer.alloc(10);
  header[0] = 0x81;
  header[1] = 127;
  header.writeBigUInt64BE(BigInt(length), 2);
  return Buffer.concat([header, body]);
}

function broadcast(payload) {
  const packet = frame(JSON.stringify(payload));

  for (const client of clients) {
    if (!client.destroyed) {
      client.write(packet);
    }
  }
}

const server = http.createServer((request, response) => {
  if (request.method !== 'POST' || request.url !== '/broadcast') {
    response.writeHead(404);
    response.end('Not found');
    return;
  }

  let body = '';

  request.on('data', chunk => {
    body += chunk;
  });

  request.on('end', () => {
    try {
      broadcast(JSON.parse(body));
      response.writeHead(200, { 'Content-Type': 'application/json' });
      response.end(JSON.stringify({ ok: true, clients: clients.size }));
    } catch (error) {
      response.writeHead(400, { 'Content-Type': 'application/json' });
      response.end(JSON.stringify({ ok: false, error: error.message }));
    }
  });
});

server.on('upgrade', (request, socket) => {
  const key = request.headers['sec-websocket-key'];

  if (!key) {
    socket.destroy();
    return;
  }

  const accept = crypto
    .createHash('sha1')
    .update(key + '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
    .digest('base64');

  socket.write([
    'HTTP/1.1 101 Switching Protocols',
    'Upgrade: websocket',
    'Connection: Upgrade',
    `Sec-WebSocket-Accept: ${accept}`,
    '',
    '',
  ].join('\r\n'));

  clients.add(socket);
  socket.on('close', () => clients.delete(socket));
  socket.on('error', () => clients.delete(socket));
});

server.listen(port, '0.0.0.0', () => {
  console.log(`WebSocket server listening on ${port}`);
});
