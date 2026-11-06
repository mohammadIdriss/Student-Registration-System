const { spawn } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');

const args = process.argv.slice(2);
function argument(name, fallback) {
  const index = args.indexOf(name);
  if (index !== -1 && args[index + 1]) return args[index + 1];
  const inline = args.find((arg) => arg.startsWith(`${name}=`));
  return inline ? inline.split('=').slice(1).join('=') : fallback;
}

const host = argument('--host', process.env.HOST || '127.0.0.1');
const port = argument('--port', process.env.PORT || '7100');
const root = path.resolve(__dirname, '..');
const candidates = [
  process.env.PHP_BINARY,
  'C:\\xampp\\php\\php.exe',
  'C:\\Program Files\\PHP\\php.exe',
  'php'
].filter(Boolean);
const php = candidates.find((candidate) => candidate === 'php' || fs.existsSync(candidate));

if (!php) {
  console.error('PHP was not found. Install PHP or set the PHP_BINARY environment variable.');
  process.exit(1);
}

const server = spawn(php, ['-S', `${host}:${port}`, '-t', root], {
  cwd: root,
  stdio: 'inherit'
});

function stop() {
  if (!server.killed) server.kill('SIGTERM');
}
process.on('SIGINT', stop);
process.on('SIGTERM', stop);
server.on('error', (error) => {
  console.error(`Could not start PHP: ${error.message}`);
  process.exit(1);
});
server.on('exit', (code) => process.exit(code || 0));
