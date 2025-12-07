# 📹 YouTube URL Downloader (Dockerized PHP/Nginx)

This project is a simple web application designed to handle the download of video streams by acting as a proxy. It is containerized using Docker and uses a PHP backend to process the URL and an Nginx frontend to serve the content and manage the web requests.

## 🚀 Getting Started

### Prerequisites

You must have **Docker Desktop** installed and running on your system.

### Running the Application

1.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/Tlkh201313/yt-url-downloader.git](https://github.com/Tlkh201313/yt-url-downloader.git)
    cd yt-url-downloader
    ```

2.  **Start the Services:**
    The application will automatically build the custom PHP backend and start both the Nginx and PHP-FPM containers.

    ```bash
    docker compose up -d
    ```

### Accessing the Website

Once the command finishes, the website will be accessible in your web browser:



➡️ **[http://localhost:5000](http://localhost:5000)**

## ⚙️ Architecture

The application runs using two main containers defined in `docker-compose.yml`:

* **`nginx` (Frontend):** Serves the static `index.html` file and routes dynamic requests (like calls to `download.php`) to the PHP backend. Exposed on local port **5000**.
* **`backend` (PHP-FPM):** A custom PHP-FPM container (built via `Dockerfile.php`) that includes **yt-dlp** for processing video URLs and handling the stream proxying logic (via `download.php`).

## 🛠 Configuration Files

We have customized the following files to ensure smooth operation:

* **`docker-compose.yml`**: Defines the two services (`nginx` and `backend`) and their networking.
* **`Dockerfile.php`**: Builds the PHP environment, installing necessary packages like `yt-dlp` via `pip`.
* **`nginx.conf`**: Configures Nginx to correctly use the container root (`/var/www/html`) and pass PHP requests to the `backend:9000` service.
