import fs from 'fs';
import path from 'path';
import sharp from 'sharp';

/**
 * Usage:
 *   node scripts/optimize-images.mjs backend/uploads
 *   node scripts/optimize-images.mjs src/assets
 *
 * Converts .png/.jpg/.jpeg to .webp (max width 1200px) alongside the original file.
 * Safe: does NOT delete originals.
 */

const inputDir = process.argv[2];
if (!inputDir) {
  console.error('Please pass a directory. Example: node scripts/optimize-images.mjs backend/uploads');
  process.exit(1);
}

const absInput = path.resolve(process.cwd(), inputDir);
if (!fs.existsSync(absInput) || !fs.statSync(absInput).isDirectory()) {
  console.error(`Not a directory: ${absInput}`);
  process.exit(1);
}

const exts = new Set(['.png', '.jpg', '.jpeg']);

function walk(dir) {
  const out = [];
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) out.push(...walk(full));
    else out.push(full);
  }
  return out;
}

const files = walk(absInput).filter((f) => exts.has(path.extname(f).toLowerCase()));

let converted = 0;
let skipped = 0;
let failed = 0;

for (const file of files) {
  const dir = path.dirname(file);
  const base = path.basename(file, path.extname(file));
  const outFile = path.join(dir, `${base}.webp`);

  if (fs.existsSync(outFile)) {
    skipped++;
    continue;
  }

  try {
    const img = sharp(file, { failOnError: false });
    const meta = await img.metadata();
    const width = meta.width || 0;

    const pipeline =
      width > 1200
        ? img.resize({ width: 1200, withoutEnlargement: true })
        : img;

    await pipeline.webp({ quality: 80 }).toFile(outFile);

    const before = fs.statSync(file).size;
    const after = fs.statSync(outFile).size;
    const saved = before > 0 ? Math.round((1 - after / before) * 100) : 0;

    converted++;
    console.log(`✔ ${path.relative(process.cwd(), file)} → ${path.relative(process.cwd(), outFile)} (${saved}% smaller)`);
  } catch (e) {
    failed++;
    console.error(`✘ Failed: ${path.relative(process.cwd(), file)} (${e.message})`);
  }
}

console.log(`\nDone. converted=${converted}, skipped=${skipped}, failed=${failed}`);



