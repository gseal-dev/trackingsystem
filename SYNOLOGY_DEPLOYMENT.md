# Deploying the Records Management System on a Synology NAS

A step-by-step guide for putting this system on your company Synology NAS using
**Container Manager** (Docker). No programming knowledge is needed — you will mostly click
in DSM and copy-paste a few commands.

> This guide replaces the older `DEPLOYMENT.MD` for the NAS. That file describes a different
> setup (a hand-built LEMP stack). Everything here is self-contained.

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
| Docker volume **`trackingsystem_db_data`** | the database (users, documents, history). Docker manages it, so you will **not** see it as a folder — the backup script (section 10) protects it. |
| Folder `data/storage` | uploaded PDF files, logs, the app key |
| File `tracking.env` | your settings and passwords |

> **Why is the database in a Docker volume and not in a folder?** On Synology, normal folders carry
> their own permission rules, and MariaDB is then not allowed to write into them (the database
> container stops with `Permission denied`). A Docker volume avoids that completely.

The program itself is rebuilt from the project files whenever you update, so it is safe to throw away.

---

## 2. Before you start

Tick every box:

- [ ] **DSM 7.2 or newer**, and your NAS model supports **Container Manager**
      (Package Center will show it; "Plus" series models are the safe choice).
- [ ] **At least 2 GB RAM** free-ish and **5 GB+ free disk space** (plus room for your PDFs).
- [ ] The **NAS has internet access** during the first install (it downloads PHP, MariaDB and the system's libraries).
- [ ] **Staff computers have internet access too.** The pages load Bootstrap, Bootstrap Icons,
      Google Fonts and Chart.js from the internet. If a PC is offline the pages will look
      broken/unstyled (the system still works). If your network blocks those sites, the fix is to
      host those files on the NAS — ask the developer.
- [ ] You have a **DSM administrator** account.
- [ ] The NAS has a **fixed IP address** (set a DHCP reservation in your router, or fix it in
      *DSM → Control Panel → Network*). Example used below: `192.168.1.50`.
- [ ] A free **port** for the system. This guide uses **8081** (DSM itself uses 5000/5001/80/443).
      You may pick another one (see *Port numbers* in section 6).
- [ ] *(For the SSH steps)* your DSM user is in the **administrators** group, so `sudo` works.

---

## 3. Install Container Manager

1. Open **Package Center**.
2. Search **Container Manager** and click **Install**.
3. After installing, DSM normally creates a shared folder called **`docker`**. All project files go in there.

**Check that it exists.** In File Station look for `docker`, or over SSH run:

```bash
ls /volume1
```

- **`docker` is listed** → fine, carry on.
- **It is missing** (this happens, for example, in Virtual DSM) → create it in the DSM web page:
  **Control Panel → Shared Folder → Create**, name `docker`, location **Volume 1**.
  Do **not** create it with `mkdir` over SSH — a folder made that way is not a Synology shared folder
  and will not appear in File Station.
- **`/volume1` does not exist** → DSM has no storage volume yet. Create one in **Storage Manager** first.

*(Optional but recommended for the SSH steps later: **Control Panel → Terminal & SNMP → Enable SSH service**.
You will log in with your DSM administrator account.)*

---

## 4. Put the project on the NAS

You need the contents of the project's **`dts`** folder (the one that contains `Dockerfile`
and `docker-compose.yml`) in:

```
/volume1/docker/trackingsystem/
```

### Option A — download a ZIP (easiest)

1. On your PC, download the **latest `ui-dev`** code from GitHub:
   `https://github.com/gseal-dev/trackingsystem/archive/refs/heads/ui-dev.zip`
2. Open **File Station** → `docker` → create a folder named **`trackingsystem`**.
3. Upload the ZIP into it (or into any folder), right-click it → **Extract**.
4. The ZIP contains `trackingsystem-ui-dev/dts/…`. **Move everything that is inside `dts`**
   up into `docker/trackingsystem`, so that `docker/trackingsystem` directly contains
   `Dockerfile`, `docker-compose.yml`, `app`, `docker`, and so on.

### Option B — Git over SSH

```bash
ssh <your-admin-user>@192.168.1.50
cd /volume1/docker
sudo git clone --branch ui-dev https://github.com/gseal-dev/trackingsystem.git repo
sudo mv repo/dts trackingsystem
```
*(Synology does not include Git by default; install the **Git Server** package if needed.)*

### Check the folder

`/volume1/docker/trackingsystem` must look like this:

```
trackingsystem/
├── Dockerfile
├── docker-compose.yml
├── tracking.env.example
├── docker/            (nginx, php, entrypoint, backup.sh ...)
├── app/  bootstrap/  config/  database/  public/  resources/  routes/ ...
└── composer.json  composer.lock  artisan
```

> If `Dockerfile`/`docker-compose.yml` are missing, the deployment files have not been added to
> the `ui-dev` branch yet. Copy them from the package you received into this folder.
> Uploading through **File Station** in your browser is the easiest way; copying with `scp` only
> works after you switch on the SFTP service in DSM.
> Do not open and re-save `docker/entrypoint.sh` in Windows Notepad: it would get Windows line endings
> and the container could no longer run it. Replace the file by uploading it instead.

---

## 5. Create your settings file (`tracking.env`)

1. In File Station, go to `docker/trackingsystem`.
2. **Copy** `tracking.env.example` and **rename the copy** to exactly **`tracking.env`**.
   *(Make sure DSM did not add `.txt` at the end — the name must end with `.env`.)*
3. Open it with **Text Editor** (install it from Package Center if it is missing) and change
   these lines:

| Line | What to put |
|---|---|
| `APP_URL=` | the exact address people will type, **including the port**, e.g. `http://192.168.1.50:8081` (it must use the same port as `docker-compose.yml` — see *Port numbers* in section 6) |
| `MARIADB_PASSWORD=` | a new password for the database user |
| `MARIADB_ROOT_PASSWORD=` | another, different password for the database admin |
| `TZ=` | your time zone (default `Asia/Manila` is already right) |

Leave everything else as it is. **Leave `APP_KEY=` empty** — it is created automatically on
the first start and saved in `data/storage/.app_key`.

**Password rules:** use only letters, numbers, `-` and `_`. Do **not** use `$`, `#`, quotes or spaces —
Docker treats them specially and the database will not accept the login. Make them long, e.g. 20 characters.

> ⚠️ **Choose the two passwords now and keep them.** MariaDB stores them the first time it starts.
> Changing them in `tracking.env` later does **not** change the database (see Troubleshooting).

---

## 6. Build and start

### Step 0 — create the storage folder (required)

Synology's Docker does **not** create missing folders for you. Without this step the start fails with
`Bind mount failed: '…/data/storage' does not exist`.

- **File Station:** open `docker/trackingsystem`, create a folder `data`, and inside it a folder `storage`.
- **or over SSH:**
  ```bash
  cd /volume1/docker/trackingsystem
  sudo mkdir -p data/storage
  ```

*(There is no `data/db` folder any more — the database lives in a Docker volume, see section 1.)*

### Port numbers — one number, three places

The system listens on **8081** unless you change it. If you want a different port (for example `8095`),
the same number must be used in **every** place below, otherwise the browser shows
*"This site can't be reached / connection refused"*:

| # | Where | What to set |
|---|---|---|
| 1 | `docker-compose.yml` → `app` → `ports:` | the **left** number, e.g. `"8095:80"` (leave the `:80` as it is) |
| 2 | `tracking.env` → `APP_URL=` | `http://<address>:8095` |
| 3 | *Only if the NAS is itself a virtual machine* (Virtual DSM) | the port must also be forwarded into it — see section 16 |

### Option A — in the DSM interface

1. Open **Container Manager → Project → Create**.
2. **Project name:** `trackingsystem`
3. **Path:** click *Set Path* and choose `/docker/trackingsystem`.
4. **Source:** choose **Use existing docker-compose.yml** (DSM detects the file in that folder).
5. Click **Next**. If asked about a *web portal / Web Station*, **do not enable it** → **Next** → **Done**.
6. DSM now **builds** the system and starts it. **The first build takes about 5–15 minutes**
   (longer on small NAS models). Wait until both containers show **green / Running**.

### Option B — over SSH

```bash
ssh <your-admin-user>@192.168.1.50
cd /volume1/docker/trackingsystem
sudo docker compose up -d --build
```
*(On older DSM 7.0/7.1 the command is `sudo docker-compose up -d --build`.)*

When it finishes, check that both containers are up:

```bash
sudo docker compose ps
```

`tracking_db` should say **healthy** and `tracking_app` **Up**. If `tracking_app` says **Restarting**, it
crashed while starting — read why with `sudo docker compose logs --tail 40 app` (see *Troubleshooting*). The very first database start can take
**1–3 minutes** (longer on a slow or virtual machine) — wait for it.

### Watch it start

Open **Container Manager → Container → `tracking_app` → Log** (or `sudo docker compose logs -f app`;
press **Ctrl+C** to stop watching — the system keeps running).
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

---

## 7. Open the system and log in

1. On any PC on your network open: **`http://192.168.1.50:8081`** (use your NAS IP and your port).
2. Log in as **`Admin`** (you can type the username `Admin` in the login box).

**Nothing opens?** First ask the NAS itself (over SSH):

```bash
curl -I http://localhost:8081/up
```

`HTTP/1.1 200` means the system is running, and the problem is only the network path to it
(wrong address or port, firewall — see *Troubleshooting*). No answer means the system itself is not
running — read the app log.

The first start creates the administrator account `Admin`. Its starting password is whatever is
set in the project's seeder file — **if you do not know it, set a new one** (this works any time):

```bash
sudo docker exec -u www-data -e HOME=/tmp tracking_app php artisan tinker --execute="App\Models\User::where('username','Admin')->first()->update(['password'=>'PUT-A-NEW-PASSWORD-HERE']); echo 'password updated';"
```

Replace `PUT-A-NEW-PASSWORD-HERE`, run it, then log in. **Do this before anyone else uses the
system** — the default account must never keep a password that is written in the source code.
Afterwards you can change passwords normally from *Users List → Edit*.

Then create your staff accounts from **Users List → Add User**.

---

## 8. Let your colleagues in (firewall)

If DSM's firewall is on (**Control Panel → Security → Firewall**), add an **Allow** rule for
**TCP port 8081**, source = your office network (e.g. `192.168.1.0/24`).

Do **not** forward the system's port on your internet router. For access from outside the office use
Synology VPN Server (or the HTTPS option below behind a proper certificate), never plain HTTP.

---

## 9. Optional: HTTPS with a friendly name

Passwords travel in plain text over `http://`. For a company LAN that is often accepted, but
HTTPS is better. DSM can do it with its **Reverse Proxy**:

1. **Control Panel → Login Portal → Advanced → Reverse Proxy → Create**
   - Source: **HTTPS**, hostname `records.yourcompany.local` (or your real domain), port **443**
   - Destination: **HTTP**, `localhost`, port **8081**
2. Assign a certificate: **Control Panel → Security → Certificate** (Let's Encrypt for a real
   domain, or an internal certificate for a `.local` name).
3. In `tracking.env` change `APP_URL` to `https://records.yourcompany.local`.
4. Tell the system it is behind a proxy. Edit **`bootstrap/app.php`** and add one line inside
   `withMiddleware`:

   ```php
   ->withMiddleware(function (Middleware $middleware): void {
       $middleware->trustProxies(at: '*');      // <-- add this line
       $middleware->alias([
           'role' => \App\Http\Middleware\EnsureRole::class,
       ]);
   })
   ```
5. Rebuild (section 12). Without step 4 the pages may try to load files over `http://` and the
   browser will block them.

---

## 10. Backups (do this on day one)

You must be able to restore **two things**: the database and the uploaded PDFs.
The file **`docker/backup.sh`** does both, and deletes backups older than 14 days.

### Set up the nightly backup

1. Open `docker/backup.sh` and check the two folders at the top
   (`PROJECT_DIR` and `BACKUP_DIR`). Create the backup folder in File Station
   (e.g. a shared folder `backup` → `trackingsystem`).
2. **Control Panel → Task Scheduler → Create → Scheduled Task → User-defined script**
   - Task: `Records backup`  ·  User: **root**
   - Schedule: **Daily**, e.g. 02:00
   - Task Settings → *Run command*:
     ```
     bash /volume1/docker/trackingsystem/docker/backup.sh
     ```
   - (Recommended) tick *Send run details by email*.
3. Select the task → **Run** once now, then check the backup folder contains
   `db_….sql.gz` and `files_….tar.gz`.

Also include the `backup` folder in **Hyper Backup** so copies leave the NAS.
Keep a safe copy of `tracking.env` too (it holds your passwords).

> The database lives in a Docker volume, so there is no database folder to copy — and copying live
> database files would not give a safe backup anyway. **Always use the script** (it takes a proper dump).
> Never run `docker compose down -v` or `docker volume rm` on a system with real data: that deletes the database.

### Restore

```bash
cd /volume1/docker/trackingsystem
# database
gunzip -c /volume1/backup/trackingsystem/db_YYYY-MM-DD_HHMM.sql.gz | \
  sudo docker exec -i tracking_db sh -c 'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"'
# uploaded files
sudo tar -xzf /volume1/backup/trackingsystem/files_YYYY-MM-DD_HHMM.tar.gz -C data
sudo docker restart tracking_app
```

---

## 11. Everyday operation

| I want to… | Do this |
|---|---|
| See if it is running | Container Manager → Project → `trackingsystem` (both containers green) |
| Read the logs | Container Manager → Container → `tracking_app` → **Log** |
| Restart | Project → `trackingsystem` → Action → **Restart** |
| Stop / start | Project → `trackingsystem` → Action → **Stop** / **Start** |
| After a NAS reboot | Nothing — both containers start by themselves (give it 1–2 minutes) |

SSH versions (run in `/volume1/docker/trackingsystem`):

```bash
sudo docker compose ps                  # status
sudo docker compose logs --tail 100 app # last 100 log lines
sudo docker compose restart             # restart everything
sudo docker compose stop                # stop
sudo docker compose start               # start
sudo docker exec -it tracking_app sh    # open a shell inside the app
```

---

## 12. Updating the system with new code

1. **Run a backup first** (Task Scheduler → select the task → Run).
2. Get the new code (same as section 4). **Copy it over the old files but keep**
   `tracking.env` and the `data` folder — do not delete them.
3. Rebuild:
   - **SSH (most reliable):**
     ```bash
     cd /volume1/docker/trackingsystem
     sudo docker compose up -d --build
     ```
   - or in DSM: Project → `trackingsystem` → Action → **Stop**, then **Build**.
4. Database changes are applied **automatically** when the app starts (you will see
   `Updating database tables` in the log). Your data and the Admin password are **not** reset.

If something goes wrong: put the previous files back, rebuild, and restore the backup (section 10).

---

## 13. Troubleshooting

| What you see | Likely cause → fix |
|---|---|
| Build fails while downloading / "could not resolve host" | The NAS has no internet or DNS. Check *Control Panel → Network → DNS* (try `8.8.8.8`), then rebuild. |
| `tracking_app` keeps **restarting**; its log ends with `file_put_contents(…/bootstrap/cache/config.php): Failed to open stream: Permission denied` (or `bootstrap/cache directory must be present and writable`) | Files uploaded through the NAS can arrive with unusable permission bits, and the web user then cannot write its cache. Use the current **`Dockerfile`** and **`docker/entrypoint.sh`** (they normalise the permissions and open the cache folders if needed) and rebuild: `sudo docker compose up -d --build`. The start-up log shows a line `note: … was not writable … opening its permissions` when it had to step in. If it still fails, run this and send the output: `sudo docker compose run --rm --entrypoint sh app -c 'id; ls -ld /var/www/html/bootstrap/cache /var/www/html/storage; ls -l /var/www/html/bootstrap/cache'` |
| `Bind mount failed: '…/data/storage' does not exist` | The storage folder was not created. Run `sudo mkdir -p data/storage` in the project folder (section 6, step 0), then start again. |
| `dependency failed to start: container tracking_db is unhealthy` | Read the database log: `sudo docker logs tracking_db --tail 40`. **`Permission denied` on `/var/lib/mysql`** means an old `docker-compose.yml` that stores the database in `./data/db`. Use the current file (database in a Docker volume), then: `sudo docker compose down`, `sudo rm -rf data/db`, `sudo docker compose up -d`. |
| Browser says **"This site can't be reached" / `ERR_CONNECTION_REFUSED`** | The address or port does not match. Run `sudo docker compose ps` and look at the `PORTS` column of `tracking_app` (e.g. `0.0.0.0:8081->80/tcp`): the number before `->` is the port to type. Then check the *Port numbers* table in section 6. On a virtual machine NAS also check section 16. |
| `port is already allocated` | Something else uses that port. In `docker-compose.yml` change the left number (e.g. `"8082:80"`), update `APP_URL` to the same number, then `sudo docker compose up -d`. |
| `tracking_app` keeps restarting; log says **`Access denied for user 'tracker'`** | The password in `tracking.env` is not the one the database was created with (see below). |
| Log says `MARIADB_… are not set` | `tracking.env` is missing, misnamed (e.g. `tracking.env.txt`) or in the wrong folder. |
| Page shows **500 Server Error** | Read the app log. For more detail set `APP_DEBUG=true` in `tracking.env`, restart, look at the page, then **set it back to `false`**. |
| Pages load but look plain / no icons | That PC cannot reach the internet CDNs (see section 2). |
| **419 Page Expired** after logging in | Open the site with the **same address** as `APP_URL`; clear cookies (especially after switching http → https). |
| Upload of a PDF fails | Maximum size is 20 MB on the server (10 MB in the form). Check the file size. |
| Others cannot open the site | Firewall rule for your port (section 8), or wrong NAS IP. Test with `curl -I http://localhost:8081/up` on the NAS first. |
| Site is slow after months of use | Container Manager → **Image** → delete unused images; check free disk space. |

**"Access denied" fix.** MariaDB takes its passwords only the *first* time it creates its database.
- If you simply mistyped: put back the password you used on the very first start, then restart.
- If it is a brand-new install with **no data yet**: remove the database volume and start again
  (this creates a fresh database with the passwords now in `tracking.env`):
  ```bash
  cd /volume1/docker/trackingsystem
  sudo docker compose down
  sudo docker volume rm trackingsystem_db_data
  sudo docker compose up -d
  ```
  ⚠️ This erases the database — never do it on a system that holds real documents.

---

## 14. Security checklist

- [ ] Set a new strong **Admin password** (section 7).
- [ ] Use long, unique passwords in `tracking.env`; keep that file private
      (only DSM administrators should have access to the `docker` shared folder).
- [ ] Passwords from a **practice or test setup** (or any that appeared in a screenshot or chat) must
      never be reused on the real NAS — choose fresh ones before the first real start.
- [ ] Do not expose the system's port to the internet. Use VPN or HTTPS behind a certificate.
- [ ] The database has **no published port** — keep it that way.
- [ ] Keep DSM, Container Manager and the system updated.
- [ ] Backups run and have been **test-restored at least once**.
- [ ] Log in as a **Staff** user and try `/admin/user-management/admins` — it must show
      "permission denied".

---

## 15. Reference

**Files delivered with this guide**

| File | Purpose |
|---|---|
| `Dockerfile` | Builds the system image (PHP 8.4 + nginx). |
| `docker-compose.yml` | Defines the two containers, the port (8081), the storage folder and the database volume. |
| `tracking.env.example` | Template for your settings and passwords. |
| `.dockerignore` | Keeps passwords and local files out of the image. |
| `docker/entrypoint.sh` | Runs at every start: folders, key, database tables, first-time data. |
| `docker/nginx.conf`, `php.ini`, `php-fpm.conf`, `supervisord.conf` | Web server and PHP settings. |
| `docker/backup.sh` | Nightly database + uploads backup. |

**Good to know**

- PHP **8.4** is required by the project's locked libraries; the image already uses it.
- The system does not need Node/Vite — there is nothing to compile for the website.
- Uploaded PDFs are stored in `data/storage/app/public/documents`.
- The database is in the Docker volume `trackingsystem_db_data` (not in `data/`).
- Synology's Docker needs the `data/storage` folder to exist before the first start (section 6, step 0).
- The seeder runs **only on the very first start**, so restarts and updates never reset
  the Admin password or your department names.

---

## 16. Practising in Virtual DSM (vDSM) before the real NAS

You can rehearse this whole guide in **Virtual DSM** — Synology's DSM 7.2 running as a virtual machine
inside a container (for example on ZimaOS, with the `vdsm/virtual-dsm` image). Everything in sections 3–14
works the same, with these differences.

> ⚠️ Synology's licence for Virtual DSM only allows it on Synology hardware. Use it for private practice
> only, never for anything real. Treat all data in it as disposable.

### 16.1 Give the virtual DSM enough power

vDSM defaults to **2 CPU cores and 2 GB RAM**, which is tight for building the image and running two
containers. In the settings (compose file) of the vDSM container add these lines (keep the ones you already
have, such as `cap_add: NET_ADMIN` and `/dev/net/tun`):

```yaml
    environment:
      RAM_SIZE: "4G"
      CPU_CORES: "2"
    devices:
      - /dev/kvm
```

Check that the host really provides `/dev/kvm`. Without it vDSM falls back to slow software emulation,
and building images becomes painfully slow. Recreate the vDSM container after changing its settings.

### 16.2 Reach the system from your PC

Inside vDSM the system listens on its port (8081, or the one you chose), but your PC can only see the
**host machine** (e.g. ZimaOS). Two ways to bridge that:

**Option 1 — forward the port** (simple). In the vDSM container settings add the port twice — as an
extra forwarded port into DSM and as a published port:

```yaml
    environment:
      USER_PORTS: "8081"
    ports:
      - 5000:5000      # the DSM web page
      - 8081:8081      # the system
```

Use your own port number if you changed it. Recreate the vDSM container afterwards.

**Option 2 — give vDSM its own IP address** on your network (macvlan, described in the vDSM project's
README). Then no port forwarding is needed at all.

Your address is then:

```
http://<IP-of-the-host-machine>:8081        (Option 1)
http://<IP-of-the-vDSM>:8081                (Option 2)
```

- Use the **host's** IP (e.g. your ZimaOS address), **not** the container's internal address such as
  `172.23.0.2` — that one only works on the host itself.
- Put the same address, with the port, in `APP_URL` in `tracking.env`.
- The DSM web page itself stays on `http://<host-IP>:5000`.

### 16.3 Logging in to DSM over SSH

From the host's terminal you can reach DSM's SSH at the container's internal address (find it with
`docker inspect <vdsm-container-name>`) and the SSH port you set in DSM. That internal address works on
the host only. The login banner "Data should only be stored in shared folders…" is DSM's standard message.

Your home folder only contains a hidden `@eaDir` folder — that is normal. The shared folders
(including `docker`) are under **`/volume1`**.

### 16.4 Find out which layer is broken

If the page does not open from your PC, run these two checks:

```bash
# inside DSM (SSH): is the system itself running?
curl -I http://localhost:8081/up
# on the host machine: does the port get through into DSM?
curl -I http://127.0.0.1:8081/up
```

`HTTP/1.1 200` means that layer is fine. If the first fails, read the `tracking_app` log. If only the
second fails, the port forwarding in 16.2 is not set up (or uses a different number).

### 16.5 What to practise, and what to expect

- Do the **whole** guide once: folders, `tracking.env`, build, login, Admin password.
- Set up the **backup task** (section 10) and do a **test restore** — this is the most valuable rehearsal.
- Do an **update** (section 12).
- The DSM firewall and Hyper Backup steps mean little in a virtual machine; just read them.
- When you move to the real NAS, repeat the steps there with **new passwords**. To carry data over,
  restore a backup (section 10) instead of copying folders.
