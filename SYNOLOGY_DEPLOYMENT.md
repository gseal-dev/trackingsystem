# Deploying the Records Management System on a Synology NAS (Simplified)

## DSM Setup

### 1. Install Packages
- Install Container Manager
  - Open Package Manager then Search for Container Manager
  - Click Install
- Install Git
  - Open Package Manager then search for Git
  - Click Install
- (Optional but Recommended) Install Nano
  1. Open Package Manager
  2. Click on Settings in the top right corner
  3. Go to the Package Sources tab and click Add.
  4. Enter the following information
   • Name: SynoCommunity
   • Location: http://packages.synocommunity.com/
  5. Click OK twice to save and close the settings
  6. In the Package Center, go to the Community tab (or refresh if it does not show up immediately).
  7. Look for SynoCli File Tools
  8. Click Install and follow the prompts to finish the setup

### 2. Enable SSH
1. Open the Control Panel
2. Click on Terminal & SNMP
3. Under the Terminal tab, check the box next to Enable SSH service.
4. (Optional but Highly Recommended) Change the Port number from the default 22 to a custom number

### 3. Connecting to SHH
1. Open your preferred terminal
2. Type:
> ssh <synology_username>@<synology_ip_address>

## Project Setup
- Once Container Manager is installed, a folder name "docker" will appear. This will house every project.

**SSH**
> cd /volume1/docker
> sudo git clone <repository_url>
> cd trackingsystem
> sudo mkdir -p data/storage
> sudo cp tracking.env.example tracking.env

**In "docker-compose.yml", change the port if necessary**
> sudo nano docker-compose.yml

**Edit "tracking.env"**
> sudo nano tracking.env

| Line | What to put |
|---|---|
| `APP_URL=` | the exact address people will type, **including the port**, e.g. `http://<Synology_ip_address>:<port_in_compose.yml>` |
| `MARIADB_PASSWORD=` | a long password for the database user |
| `MARIADB_ROOT_PASSWORD=` | another, different, long password for the database admin |
| `TZ=` | your time zone (default `Asia/Manila` is already right) |

Leave everything else as it is. **Leave `APP_KEY=` empty** — a key is created on the first start and saved in
`data/storage/.app_key`.

**Password rules:** use only letters, numbers, `-` and `_`. Do **not** use `$`, `#`, quotes or spaces —
Docker treats them specially and the login to the database will fail. About 20 characters is good.

### Docker

> sudo docker compose up -d --build

Wait for the build to finish, then check if the app is Healthy

> curl -I http://<synology_ip_address>:<port>/up

`HTTP/1.1 200 OK` means it is running.
---

## 4. Backups (set up on day one)

You must be able to restore **two things**: the database and the uploaded PDF files. `docker/backup.sh` does both and keeps the last 14 days.

### 4.1 Set up the nightly backup
1. Create a backup folder in File Station (for example a shared folder `backup`, and inside it `trackingsystem`), or over SSH: `sudo mkdir -p /volume1/backup/trackingsystem`.
2. Open `docker/backup.sh` and check the two folders at the top (`PROJECT_DIR`, `BACKUP_DIR`).
3. **Control Panel → Task Scheduler → Create → Scheduled Task → User-defined script**
   - Task: `Records backup` · User: **root**
   - Schedule: **Daily**, e.g. **02:00**
   - Task Settings → *Run command*:
     ```
     bash /volume1/docker/trackingsystem/docker/backup.sh
     ```
   - (Recommended) tick *Send run details by email*.
4. Select the task → **Run** once now. The backup folder must then contain `db_….sql.gz` and `files_….tar.gz`.

Also include the backup folder in **Hyper Backup** so copies leave the NAS, and keep a safe copy of `tracking.env` (it holds your passwords).

> The database lives in a Docker volume, not a folder you can copy — and copying live database files would not give a safe backup anyway. **Always use the script.** Never run `docker compose down -v` or `docker volume rm` on a system with real data: that deletes the database.

### 4.2 Restore (if you ever need it)
```bash
cd /volume1/docker/trackingsystem
# 1) the database (replaces the current content)
gunzip -c /volume1/backup/trackingsystem/db_YYYY-MM-DD_HHMMSS.sql.gz | \
  sudo docker exec -i tracking_db sh -c 'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"'
# 2) the uploaded files
sudo tar -xzf /volume1/backup/trackingsystem/files_YYYY-MM-DD_HHMMSS.tar.gz -C data
# 3) restart the app
sudo docker restart tracking_app
```

---

## 5. Keeping it running day to day

| I want to… | Do this |
|---|---|
| See if it is running | `sudo docker compose ps` (or Container Manager → Container) |
| Read the logs | `sudo docker compose logs --tail 100 app` |
| Restart | `sudo docker compose restart` |
| Stop / start | `sudo docker compose stop` / `sudo docker compose start` |
| Open a shell in the app | `sudo docker exec -it tracking_app sh` |
| After a NAS reboot | Nothing — both containers start by themselves (give it 1–2 minutes) |

Run these commands in `/volume1/docker/trackingsystem`.

### Updating with new code
If you cloned with Git (the setup commands above), you can just pull — otherwise download
the ZIP and copy it over, keeping your local files.

1. **Run a backup first** (Task Scheduler → select the task → Run).
2. **Pull the new code**
   ```bash
   cd /volume1/docker/trackingsystem
   sudo git pull
   ```
   *(If you downloaded a ZIP instead, copy the new files over the old ones here.)*

   **Keep your local files:** do not overwrite `tracking.env`, the `data` folder, or your edited `docker-compose.yml` if you changed the port. If `git pull` complains about local changes, stash them first, pull, then restore: `sudo git stash && sudo git pull`.

3. **Rebuild**
   ```bash
   sudo docker compose up -d --build
   ```
4. Database changes are applied **automatically** when the app starts (`Updating database tables` in the log). Your data and passwords are **not** reset — the default data is only created on the very first start.

---

## 6. Quick fixes for common problems

| What you see | Cause → fix |
|---|---|
| `Bind mount failed: '…/data/storage' does not exist` | Run `sudo mkdir -p data/storage`, then `sudo docker compose up -d`. |
| `tracking_app` keeps restarting / log says `Access denied for user 'tracker'` | The passwords in `tracking.env` are not the ones created on the first start (they cannot be changed later without resetting the database). Put the original passwords back. |
| Browser says **"This site can't be reached"** | Address or port does not match, or the app is not running. Check `sudo docker compose ps` → `PORTS` column of `tracking_app` (e.g. `0.0.0.0:8081->80/tcp`). The number before `->` is the port to type. Then run `curl -I http://localhost:<port>/up` on the NAS. If that works, the problem is only the network path (firewall, wrong IP). |
| `port is already allocated` | Change the left number in `docker-compose.yml` (e.g. `"8082:80"`), set `APP_URL` to the same number, then `sudo docker compose up -d`. |
| Page shows **500 Server Error** | Read the logs (`sudo docker compose logs --tail 40 app`). For more detail set `APP_DEBUG=true` in `tracking.env`, restart, look at the page, then **set it back to `false`**. |
| Others cannot open the site | Add a firewall **Allow** rule for TCP port 8081 (**Control Panel → Security → Firewall**), source your office network (e.g. `192.168.1.0/24`). |

---

## 7. Security basics

- [ ] Set a new strong **Admin password** right after the first login (section 4).
- [ ] Keep `tracking.env` private — only DSM administrators should have access to the `docker` shared folder (it holds your passwords).
- [ ] Do **not** expose the system's port (e.g. 8081) to the internet. For outside access, use Synology's VPN Server or HTTPS behind a proper certificate.
- [ ] Backups run nightly and have been **test-restored at least once**.
- [ ] Switch off SSH when you do not need it (**Control Panel → Terminal & SNMP**).

