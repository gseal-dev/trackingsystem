# 📁 DOCUMENT TRACKING SYSTEM


# First Time Setup

1. Clone the Project

    Clone the repository from GitHub to your local machine. 
    ```bash
    git clone https://github.com/YourUsername/Document-Tracking-System.git  
    ```
2. Go to the Project Directory

    Navigate to the project folder where the Laravel app is located.
    ```bash
    cd Document-Tracking-System/dts

3. Install Dependencies

   Install all required Laravel dependencies using Composer.
    ```bash
    composer install

4. Copy Environment File

   Create a new .env file by copying the example provided.
   ```bash
   cp .env.example .env

5. Configure Your Database in .env File

    Open the .env file and update your database name, username, and password.

   Then, run the setup command to initialize the project and database.
    ```bash
    php artisan project:setup --fresh


This will automatically create the default roles, departments, and document statuses.

---
## Manual Setup (Alternative)

If the setup command fails or is unavailable, you can manually run each step below:
  ```bash
    php artisan migrate
  ```
    
  ```bash
    php artisan db:seed
  ```

  ```bash
    php artisan key:generate
  ```

  ```bash
    php artisan storage:link
  ```

  ```bash
    php artisan serve
  ```


# To Push

1. Click Source Control (Left side of the window under the search)
2. Hover over changes and click the Stage all Changes (+ sign)
3. In Terminal

```bash
git status
```

```bash
git add .
```

```bash
git commit -m "Message on what you did"
```

```bash
git push origin
```

# To Pull

To sync your local project with the latest changes from GitHub:
```bash
git pull origin main
```




