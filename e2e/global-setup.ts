import { execSync } from 'node:child_process';
import 'dotenv/config';

export default async function globalSetup() {
  const cmd = process.env.E2E_RESET_CMD;
  if (cmd) execSync(cmd, { stdio: 'inherit', cwd: process.env.APP_DIR ?? '..' });
}
