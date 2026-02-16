# ARC Task Manager

![Project Status](https://img.shields.io/badge/STATUS-OPERATIONAL-success?style=for-the-badge)
![Laravel](https://img.shields.io/badge/CORE-LARAVEL-red?style=for-the-badge)
![Tailwind](https://img.shields.io/badge/VISUALS-TAILWINDCSS-blue?style=for-the-badge)

A high-fidelity, tactical task management interface inspired by the retro-tech aesthetic of **ARC Raiders**. Ideally suited for operatives who need to manage directives with efficiency and style.

## 📡 Transmission Integrity
- **Tactical UI**: Dark mode-first design with cohesive orange/monochrome palettes (`arc-orange`, `arc-ink`, `arc-paper`).
- **Interactive Feedback**: Glitch effects, scanning animations, and responsive micro-interactions.
- **Mission Control HUD**: Real-time visualization of current objectives, threats (critical tasks), and efficiency ratings.

## 🛠️ System Capabilities (Features)
- **Directive Management**: Initiate (Create), Modify (Edit), and Terminate (Delete) tasks.
- **Priority Protocols**: Catagorize threats by Low, Medium, High, and **CRITICAL** urgency levels.
- **Sector Navigation**: Pagination and sorting to manage large volumes of directives.
- **Status Tracking**: Mark directives as executed/complete or pending.
- **Search & Filter**: Rapidly locate specific directives within the database.

## 📋 Pre-requisites
Ensure your terminal is equipped with:
- PHP >= 8.2
- Composer
- Node.js & NPM

## 🚀 Initialization Sequence (Setup)

1.  **Clone the Repository**
    ```bash
    git clone <repository-url>
    cd task-manager
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database settings in the `.env` file.*

4.  **Database Migration**
    ```bash
    touch database/database.sqlite # If using SQLite
    php artisan migrate
    ```

5.  **Compile Assets**
    ```bash
    npm run build
    ```

## ⚡ Engagement (Running Locally)

To begin operations, execute the following commands in separate terminal instances:

**1. Asset Watcher (Vite)**
```bash
npm run dev
```

**2. Local Server (Artisan)**
```bash
php artisan serve
```

Access the interface via `http://127.0.0.1:8000`.

## 🤝 Contribution
Directives for contribution are open. Fork the repository and submit a Pull Request for review by Command.

## 📄 License
Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
