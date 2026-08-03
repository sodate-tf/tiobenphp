const fs = require('fs');
const zlib = require('zlib');
const src = 'C:/xampp/htdocs/iatioben/mobile/build-log-a7261d29.txt';
const dst = 'C:/xampp/htdocs/iatioben/mobile/build-log-a7261d29.decoded.txt';
const buf = fs.readFileSync(src);
let out;
try {
  out = zlib.brotliDecompressSync(buf).toString('utf8');
  console.log('DECODE_OK');
} catch (err) {
  console.log('DECODE_FAIL');
  console.log(String(err && err.message || err));
  out = buf.toString('utf8');
}
fs.writeFileSync(dst, out);
const lines = out.split(/\r?\n/);
console.log(lines.slice(-220).join('\n'));
