# Deploying the Records Management System on a Synology NAS

Step-by-step guide for running this system on your company **Synology NAS (DSM 7.2 or newer)**
with **Container Manager** (Docker). You do not need to be a programmer: you will click in DSM and
copy-paste a handful of commands.

> **Status of this guide.** The Docker files were built and run end-to-end on a *Virtual DSM 7.2*
> test machine, and the database setup, restart behaviour, backup and restore were tested separately.
> The first run on a *physical* Synology has not been done yet, so a DSM menu may be worded slightly
> differently on your model. If anything fails, the **Troubleshooting** section (14) lists every
> problem we met during testing, with the fix.

---

## 1. How it works (the 30-second version)

```
 Staff / Admin browser
        │   http://<NAS-IP>:8081
        ▼
 ┌──────────────────────────────── Synology NAS ───────────────────────────────┐
 │  tracking_app   (web server + PHP + the system)                              │
 │        │                                                                     │
 │        ▼  (private, not reachable from the network)                          │
 │  tracking_db    (MariaDB database)                                           │
 └──────────────────────────────────────────────────────────────────────────────┘
```

Everything you must never lose lives in three places:

| Where | What it contains |
|---|---|
| Docker volume **`trackingsystem_db_data`** | the database (users, documents, history). Docker manages it, so you will **not** see it as a folder — the backup script (section 12) protects it. |
| Folder **`data/storage`** | uploaded PDF files, logs, the application key |
| File **`tracking.env`** | your settings and passwords |

> **Why is the database in a Docker volume and not in a folder?** On Synology, normal folders carry
> their own permission rules, and MariaDB is then not allowed to write into them (the database container
> stops with `Permission denied`). A Docker volume avoids that completely.

The program itself is rebuilt from the project files whenever you update, so it is safe to throw away.

---

## 2. Before you start

Tick every box:

- [ ] **DSM 7.2 or newer**, and your NAS model supports **Container Manager**
      (Package Center shows it; "Plus"-series models are the safe choice).
- [ ] **At least 2 GB of RAM** and **5 GB+ free disk space** (plus room for your PDFs).
- [ ] The **NAS has internet access** during the first install (it downloads PHP, MariaDB and the libraries).
- [ ] **Staff computers have internet access too.** The pages load Bootstrap, Bootstrap Icons,
      Google Fonts and Chart.js from the internet. On an offline PC the pages look unstyled (the system
      still works). If your network blocks those sites, the fix is to host those files on the NAS — ask the developer.
- [ ] You have a **DSM administrator** account (needed for `sudo` over SSH).
- [ ] The NAS has a **fixed IP address** (DHCP reservation in your router, or *Control Panel → Network*).
      Example used below: `192.168.1.50`.
- [ ] A free **port** for the system. This guide uses **8081** (DSM itself uses 5000/5001/80/443).
- [ ] **You have decided on two long database passwords** and written them down (see section 6.2 —
      they cannot be changed later without resetting the database).

---

## 3. Install Container Manager

1. Open **Package Center**, search **Container Manager** and click **Install**.
2. DSM normally creates a shared folder called **`docker`**. All project files go in there.

**Check that the folder exists.** In File Station look for `docker`, or over SSH (section 4) run:

```bash
ls /volume1
```

- **`docker` is listed** → fine.
- **It is missing** → create it in the DSM web page: **Control Panel → Shared Folder → Create**,
  name `docker`, location **Volume 1**. Do **not** create it with `mkdir` over SSH — a folder made
  that way is not a Synology shared folder and does not show up in File Station.
- **`/volume1` does not exist** → your NAS has no storage volume yet; create one in **Storage Manager**.
- If your volume is called something else (e.g. `/volume2`), use that name in every path below.

---

## 4. Switch on SSH (recommended)

Most steps below are easiest over SSH, and it is the route that was tested.

1. **Control Panel → Terminal & SNMP → Enable SSH service** (leave the port at 22 unless you changed it).
2. From your PC (Windows Terminal / PowerShell / Mac Terminal):
   ```bash
   ssh <your-admin-user>@192.168.1.50
   ```
   Only members of the **administrators** group can log in over SSH. Use `sudo` in front of commands that need it.
3. DSM prints a warning banner ("Using terminal commands to modify system configs…") — that is normal.
   Your home folder only contains a hidden `@eaDir` folder — also normal; the shared folders are under `/volume1`.

You can switch SSH off again when you are done (Control Panel → Terminal & SNMP).

---

## 5. Put the project on the NAS

You need the contents of the project's **`dts`** folder (the one containing `artisan` and `composer.json`)
in:

```
/volume1/docker/trackingsystem/
```

### 5.1 Get the code — pick one option

**Option A — download a ZIP (easiest)**

1. On your PC, download the latest code:
   `https://github.com/gseal-dev/trackingsystem/tree/main`
2. **File Station** → `docker` → create a folder **`trackingsystem`**.
3. Upload the ZIP into it (or anywhere), right-click → **Extract**.
4. The ZIP contains `trackingsystem/dts/…`. **Move everything that is inside `dts`** up into
   `docker/trackingsystem`, so that `docker/trackingsystem` directly contains `artisan`, `app`, `composer.json`, …

**Option B — Git over SSH**

```bash
cd /volume1/docker
sudo git clone https://github.com/gseal-dev/trackingsystem.git repo
sudo mv repo/dts trackingsystem
```
*(Synology does not include Git by default; install the **Git Server** package if needed.)*



> ⚠️ Do **not** open and re-save `docker/entrypoint.sh` (or `backup.sh`) in Windows Notepad: it would add
> Windows line endings and the container could no longer run it. Replace those files by uploading them instead.
> *(`.dockerignore` starts with a dot, so File Station may hide it. Upload it anyway: it keeps development leftovers, such as a stray `.env` file or old cache files, out of the system image.)*

### 5.3 Check the folder

`/volume1/docker/trackingsystem` must look like this:

```
trackingsystem/
├── Dockerfile
├── docker-compose.yml
├── tracking.env.example
├── docker/            (nginx, php, entrypoint, backup.sh ...)
├── app/  bootstrap/  config/  database/  public/  resources/  routes/ ...
└── artisan  composer.json  composer.lock
```

Check from SSH (`-a` also shows files starting with a dot, such as `.dockerignore`):

```bash
cd /volume1/docker/trackingsystem
ls -a
```

---

## 6. Prepare the settings and the storage folder

### 6.1 Create the storage folder (required)

Synology's Docker does **not** create missing folders. Without this step the start fails with
`Bind mount failed: '…/data/storage' does not exist`.

```bash
cd /volume1/docker/trackingsystem
sudo mkdir -p data/storage
```

*(There is no `data/db` folder — the database lives in a Docker volume, see section 1.)*

### 6.2 Create your settings file (`tracking.env`)

```bash
cd /volume1/docker/trackingsystem
sudo cp tracking.env.example tracking.env
```

Then edit it — either over SSH with `sudo nano tracking.env` (Ctrl+O, Enter to save, Ctrl+X to leave),
or in File Station with the **Text Editor** package (Package Center). Change these lines:

| Line | What to put |
|---|---|
| `APP_URL=` | the exact address people will type, **including the port**, e.g. `http://192.168.1.50:8081` |
| `MARIADB_PASSWORD=` | a long password for the database user |
| `MARIADB_ROOT_PASSWORD=` | another, different, long password for the database admin |
| `TZ=` | your time zone (default `Asia/Manila` is already right) |

Leave everything else as it is. **Leave `APP_KEY=` empty** — a key is created on the first start and saved in
`data/storage/.app_key`.

**Password rules:** use only letters, numbers, `-` and `_`. Do **not** use `$`, `#`, quotes or spaces —
Docker treats them specially and the login to the database will fail. About 20 characters is good.

> ⚠️ **Choose the two passwords now and never change them in this file afterwards.**
> The database stores them the first time it starts. Editing `tracking.env` later does **not** change the
> database, and the system would stop with *"Access denied"* (see Troubleshooting for the fix).

### 6.3 Port numbers — one number, in two places

The system listens on **8081** unless you change it. To use another port (for example `8095`), use the same
number in **both** places, otherwise the browser says *"This site can't be reached / connection refused"*:

| # | Where | What to set |
|---|---|---|
| 1 | `docker-compose.yml` → `app` → `ports:` | the **left** number, e.g. `"8095:80"` (leave the `:80` as it is) |
| 2 | `tracking.env` → `APP_URL=` | `http://<address>:8095` |

---

## 7. Build and start

### Option A — over SSH (tested)

```bash
cd /volume1/docker/trackingsystem
sudo docker compose up -d --build
```
*(On older DSM 7.0/7.1 the command is `sudo docker-compose up -d --build`.)*

The **first build takes about 5–15 minutes** (longer on small NAS models): it downloads PHP and MariaDB and
installs the libraries. Later rebuilds take seconds because Docker reuses what it already has.

### Option B — in the DSM interface

1. **Container Manager → Project → Create**.
2. **Project name:** `trackingsystem` · **Path:** `/docker/trackingsystem`.
3. **Source:** *Use existing docker-compose.yml*.
4. **Next**. If asked about a *web portal / Web Station*, do **not** enable it → **Next → Done**.
5. DSM builds and starts the system. Wait until both containers are green / *Running*.

*(Only the SSH route was tested; the wording of the DSM screens may differ slightly.)*

---

## 8. Check that it works

```bash
cd /volume1/docker/trackingsystem
sudo docker compose ps
```

`tracking_db` should say **healthy** and `tracking_app` **Up**. The very first database start can take
**up to a minute or two** — wait for it. If `tracking_app` says **Restarting**, it crashed while starting:
read why with `sudo docker compose logs --tail 40 app` and see *Troubleshooting*.

Watch the start-up (press **Ctrl+C** to stop watching — the system keeps running):

```bash
sudo docker compose logs -f app
```

A healthy first start prints these lines and then stays quiet:

```
==> Preparing storage folders
==> Checking settings
==> Generating a new application key (saved in data/storage/.app_key)
==> Linking public storage (uploaded files)
==> Waiting for the database
==> Updating database tables
==> First start: creating default roles, departments and the Admin account
==> Optimizing for production
==> Starting web server
```

*(You may also see a line `note: … opening its permissions` — harmless, it means the start-up script fixed
folder permissions itself.)*

Then ask the NAS itself:

```bash
curl -I http://localhost:8081/up
```

`HTTP/1.1 200 OK` means the system is running (use your own port if you changed it).

---

## 9. Log in for the first time

1. On any PC on your network open **`http://192.168.1.50:8081`** (your NAS IP and port).
2. Log in as **`Admin`** (type `Admin` in the login box).

The first start creates the administrator account `Admin`. Its starting password is whatever is set in the
project's seeder file — **if you do not know it, set a new one** (works any time):

```bash
sudo docker exec -u www-data -e HOME=/tmp tracking_app php artisan tinker --execute="App\Models\User::where('username','Admin')->first()->update(['password'=>'PUT-A-NEW-PASSWORD-HERE']); echo 'password updated';"
```

Replace `PUT-A-NEW-PASSWORD-HERE`, run it, then log in. **Do this before anyone else uses the system** —
a default account must never keep a password that is written in the source code. Afterwards you can change
passwords normally from *Users List → Edit*.

Then create your staff accounts from **Users List → Add User**.

---

## 10. Let your colleagues in

- **Firewall:** if DSM's firewall is on (**Control Panel → Security → Firewall**), add an **Allow** rule for
  **TCP port 8081** (your port), source = your office network (e.g. `192.168.1.0/24`).
- **Do not** forward the system's port on your internet router. For access from outside the office use
  Synology's VPN Server (or HTTPS behind a proper certificate, section 11) — never plain HTTP over the internet.
- Give people the address `http://192.168.1.50:8081` (a browser bookmark is enough).

---

## 11. Optional: HTTPS with a friendly name

Passwords travel in plain text over `http://`. For a company LAN that is often accepted, but HTTPS is better.
DSM can do it with its **Reverse Proxy**:

1. **Control Panel → Login Portal → Advanced → Reverse Proxy → Create**
   - Source: **HTTPS**, hostname `records.yourcompany.local` (or your real domain), port **443**
   - Destination: **HTTP**, `localhost`, port **8081** (your port)
2. Assign a certificate: **Control Panel → Security → Certificate** (Let's Encrypt for a real domain,
   or an internal certificate for a `.local` name).
3. In `tracking.env` change `APP_URL` to `https://records.yourcompany.local`.
4. Tell the system it sits behind a proxy. Edit **`bootstrap/app.php`** and add one line inside `withMiddleware`:

   ```php
   ->withMiddleware(function (Middleware $middleware): void {
       $middleware->trustProxies(at: '*');      // <-- add this line
       $middleware->alias([
           'role' => \App\Http\Middleware\EnsureRole::class,
       ]);
   })
   ```
5. Rebuild (section 13). Without step 4 the pages may try to load files over `http://` and the browser blocks them.

*(This HTTPS setup has not been tested yet.)*

---

## 12. Backups (do this on day one)

You must be able to restore **two things**: the database and the uploaded PDFs.
`docker/backup.sh` does both, keeps the last 14 days, and refuses to save a broken (empty) dump.
A failed run can never damage a good backup. It was tested, including a full restore into an empty database.

### 12.1 Set up the nightly backup

1. Create the backup folder in File Station (for example a shared folder `backup`, and inside it `trackingsystem`),
   or over SSH: `sudo mkdir -p /volume1/backup/trackingsystem`.
2. Open `docker/backup.sh` and check the two folders at the top (`PROJECT_DIR`, `BACKUP_DIR`).
3. **Control Panel → Task Scheduler → Create → Scheduled Task → User-defined script**
   - Task: `Records backup` · User: **root**
   - Schedule: **Daily**, e.g. 02:00
   - Task Settings → *Run command*:
     ```
     bash /volume1/docker/trackingsystem/docker/backup.sh
     ```
   - (Recommended) tick *Send run details by email*.
4. Select the task → **Run** once now. The backup folder must then contain
   `db_….sql.gz` and `files_….tar.gz`.

Also include the `backup` folder in **Hyper Backup** so copies leave the NAS, and keep a safe copy of
`tracking.env` (it holds your passwords).

> The database lives in a Docker volume, so there is no database folder to copy — and copying live database
> files would not give a safe backup anyway. **Always use the script.**
> Never run `docker compose down -v` or `docker volume rm` on a system with real data: that deletes the database.

### 12.2 Restore

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

**Test a restore once** (for example on a test copy) before you rely on the backups.

---

## 13. Everyday operation and updates

### 13.1 Everyday

| I want to… | Do this |
|---|---|
| See if it is running | `sudo docker compose ps` (or Container Manager → Container) |
| Read the logs | `sudo docker compose logs --tail 100 app` (or Container Manager → `tracking_app` → Log) |
| Restart | `sudo docker compose restart` |
| Stop / start | `sudo docker compose stop` / `sudo docker compose start` |
| Open a shell in the app | `sudo docker exec -it tracking_app sh` |
| After a NAS reboot | Nothing — both containers start by themselves (give it 1–2 minutes) |

Run the `docker compose …` commands in `/volume1/docker/trackingsystem`.

### 13.2 Updating the system with new code

1. **Run a backup first** (Task Scheduler → select the task → Run).
2. **Get the new code.**

   If you installed with Git (section 5.1, Option B), just pull:

   ```bash
   cd /volume1/docker/trackingsystem
   sudo git pull
   ```

   Otherwise, download the new code (same as section 5.1) and copy it over the old files.

   **Keep your local files:** do not overwrite `tracking.env`, the `data` folder, or your edited `docker-compose.yml` if you changed the port. If `git pull` complains about local changes, stash them first, pull, then restore: `sudo git stash && sudo git pull`.

3. **Rebuild**
   ```bash
   sudo docker compose up -d --build
   ```
4. Database changes are applied **automatically** when the app starts (`Updating database tables` in the log).
   Your data and the Admin password are **not** reset — the default data is only created on the very first start.

If something goes wrong: put the previous files back, rebuild, and restore the backup (12.2).

---

## 14. Troubleshooting

Every problem below really happened during testing.

| What you see | Cause → fix |
|---|---|
| `Bind mount failed: '…/data/storage' does not exist` | The storage folder was not created. Run `sudo mkdir -p data/storage` (6.1), then `sudo docker compose up -d`. |
| `dependency failed to start: container tracking_db is unhealthy` | Read the database log: `sudo docker logs tracking_db --tail 40`. **`Permission denied` on `/var/lib/mysql`** means an old `docker-compose.yml` that stores the database in `./data/db`. Use the current file (database in the Docker volume `db_data`), then: `sudo docker compose down`, `sudo rm -rf data/db`, `sudo docker compose up -d`. |
| `tracking_app` log repeats **`Access denied for user 'tracker'`** and finally *"the database did not become reachable after 20 tries"* | The passwords in `tracking.env` are not the ones the database was created with (they were changed after the first start). Put the original passwords back — or, on a **brand-new install with no data**, reset the database (below). |
| `tracking_app` keeps **restarting**; log ends with `file_put_contents(…/bootstrap/cache/config.php): Failed to open stream: Permission denied` (or `bootstrap/cache directory must be present and writable`) | Files uploaded through the NAS can arrive with unusable permission bits. Use the current **`Dockerfile`** and **`docker/entrypoint.sh`** from this package (they normalise permissions) and rebuild: `sudo docker compose up -d --build`. If it still fails: `sudo docker compose run --rm --entrypoint sh app -c 'id; ls -ld /var/www/html/bootstrap/cache /var/www/html/storage; ls -l /var/www/html/bootstrap/cache'` and send the output to the developer. |
| Browser says **"This site can't be reached" / `ERR_CONNECTION_REFUSED`** | Address or port does not match, or the app is not running. `sudo docker compose ps` → look at the `PORTS` column of `tracking_app` (e.g. `0.0.0.0:8081->80/tcp`): the number before `->` is the port to type. Then `curl -I http://localhost:8081/up` on the NAS: if that works, the problem is only the network path (firewall, wrong IP). |
| `port is already allocated` | Something else uses that port. In `docker-compose.yml` change the left number (e.g. `"8082:80"`), set `APP_URL` to the same number, then `sudo docker compose up -d`. |
| Build fails while downloading / "could not resolve host" | The NAS has no internet or DNS. Check *Control Panel → Network → DNS* (try `8.8.8.8`) and run the build again. |
| `docker: 'compose' is not a docker command` | Older DSM: use `sudo docker-compose …` (with a hyphen). |
| Log says `MARIADB_… are not set` | `tracking.env` is missing, misnamed (e.g. `tracking.env.txt`) or in the wrong folder. |
| Page shows **500 Server Error** | Read the app log. For more detail set `APP_DEBUG=true` in `tracking.env`, `sudo docker compose up -d --force-recreate app`, look at the page, then **set it back to `false`**. |
| Pages load but look plain / no icons | That PC cannot reach the internet CDNs (section 2). |
| **419 Page Expired** after logging in | Open the site with the **same address** as `APP_URL`; clear cookies (especially after switching http → https). |
| Upload of a PDF fails | Maximum size is 20 MB on the server (10 MB in the form). Check the file size. |
| Others cannot open the site | Firewall rule for your port (section 10), or wrong NAS IP. Test with `curl -I http://localhost:8081/up` on the NAS first. |
| Site is slow after months of use | Container Manager → **Image** → delete unused images; check free disk space. |

**Resetting the database** (only for a brand-new install with **no data yet** — it erases everything in the database):

```bash
cd /volume1/docker/trackingsystem
sudo docker compose down
sudo docker volume rm trackingsystem_db_data
sudo docker compose up -d
```

MariaDB then creates a fresh database with the passwords currently in `tracking.env`.

---

## 15. Security checklist

- [ ] Set a new strong **Admin password** (section 9).
- [ ] Use long, unique passwords in `tracking.env`; keep that file private
      (only DSM administrators should have access to the `docker` shared folder).
- [ ] Passwords from a **practice or test setup** (or any that appeared in a screenshot or chat) are never reused here.
- [ ] Do not expose the system's port to the internet. Use VPN or HTTPS behind a certificate.
- [ ] The database has **no published port** — keep it that way.
- [ ] Keep DSM, Container Manager and the system updated.
- [ ] Backups run, and have been **test-restored at least once**.
- [ ] Log in as a **Staff** user and try `/admin/user-management/admins` — it must show "permission denied".
- [ ] Switch off SSH when you do not need it.

---

## 16. Reference

**Files in this package**

| File | Purpose |
|---|---|
| `Dockerfile` | Builds the system image (PHP 8.4 + nginx); normalises file permissions. |
| `docker-compose.yml` | Defines the two containers, the port (8081), the storage folder and the database volume. |
| `tracking.env.example` | Template for your settings and passwords (copy it to `tracking.env`). |
| `.dockerignore` | Keeps passwords and local files out of the image. |
| `docker/entrypoint.sh` | Runs at every start: folders and permissions, key, database tables, first-time data. |
| `docker/nginx.conf`, `php.ini`, `php-fpm.conf`, `supervisord.conf` | Web server and PHP settings. |
| `docker/backup.sh` | Nightly database + uploads backup. |

**Good to know**

- PHP **8.4** is required by the project's locked libraries; the image already uses it.
- The system does not need Node/Vite — there is nothing to compile for the website.
- Uploaded PDFs are stored in `data/storage/app/public/documents`.
- The default data (roles, departments, `Admin` account) is created **only on the very first start**, so restarts
  and updates never reset the Admin password or your department names.
- If you commit these files to git, add these two lines to `.gitignore` so passwords and live data are never committed:
  ```
  /tracking.env
  /data/
  ```

**Moving from a practice setup (e.g. Virtual DSM) to the real NAS**

- Repeat sections 5–9 on the real NAS with **new passwords**.
- To carry data over, restore a backup (12.2) instead of copying folders.
- Remove the practice setup's port forwarding; on a real Synology the address is simply `http://<NAS-IP>:8081`.
