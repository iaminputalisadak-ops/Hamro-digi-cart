#!/usr/bin/env node
/**
 * Deploy via SSH/SCP
 * 1. Builds React app
 * 2. Uploads build/ and backend/ via SCP
 *
 * Edit the config below with your SSH details.
 * Run: npm run deploy:ssh
 */

import { execSync } from 'child_process';
import { existsSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '..');

// SSH config - edit these
const SSH_CONFIG = {
  host: 'hamrodigicart.com',           // or your cPanel SSH host
  user: 'hamrodig',
  remotePath: '/home8/hamrodig/public_html',
};

function build() {
  console.log('\n[1/2] Building React app...');
  execSync('npm run build', { cwd: ROOT, stdio: 'inherit' });
  console.log('      ✓ Build complete\n');
}

function deploy() {
  const buildDir = join(ROOT, 'build');
  const backendDir = join(ROOT, 'backend');

  if (!existsSync(buildDir)) {
    console.error('ERROR: build/ not found. Run npm run build first.');
    process.exit(1);
  }

  const dest = `${SSH_CONFIG.user}@${SSH_CONFIG.host}:${SSH_CONFIG.remotePath}`;

  console.log('[2/2] Uploading via SCP...');
  console.log('      (You may be prompted for SSH password)\n');

  try {
    // Upload build contents to public_html
    execSync(`scp -r ${join(ROOT, 'build')}/* ${dest}/`, {
      stdio: 'inherit',
      shell: true,
    });
    console.log('      ✓ Frontend uploaded\n');

    // Upload backend to public_html/backend
    execSync(`scp -r ${join(ROOT, 'backend')}/* ${dest}/backend/`, {
      stdio: 'inherit',
      shell: true,
    });
    console.log('      ✓ Backend uploaded\n');

    console.log('========================================');
    console.log('  Deploy complete!');
    console.log('  https://hamrodigicart.com');
    console.log('========================================\n');
  } catch (err) {
    console.error('\nDeploy failed. Check SSH host, user, and path.');
    process.exit(1);
  }
}

async function main() {
  console.log('========================================');
  console.log('  Deploy via SSH');
  console.log(`  ${SSH_CONFIG.user}@${SSH_CONFIG.host}`);
  console.log('========================================');
  build();
  deploy();
}

main();
