# Deploy via SSH

Deploy your code to cPanel using SSH/SCP (no FTP needed).

## 1. Edit SSH config

Open `scripts/deploy-ssh.mjs` and update:

```javascript
const SSH_CONFIG = {
  host: 'hamrodigicart.com',      // Your cPanel SSH host
  user: 'hamrodig',               // Your cPanel username
  remotePath: '/home8/hamrodig/public_html',  // Path to public_html
};
```

**Find your SSH host:** In cPanel → SSH Access, it shows the host (e.g. `hamrodigicart.com` or `box123.yourhost.com`).

## 2. Run deploy

```bash
npm run deploy:ssh
```

You'll be prompted for your SSH password when SCP uploads files.

## 3. Passwordless deploy (optional)

To avoid entering the password each time:

1. **On your PC**, generate a key: `ssh-keygen -t rsa`
2. Copy your public key to the server:
   ```bash
   ssh-copy-id hamrodig@hamrodigicart.com
   ```
3. Now `npm run deploy:ssh` won't ask for a password.

---

**Note:** Your local code is NOT automatically connected to cPanel. You deploy by running this script—it builds and uploads files each time.
