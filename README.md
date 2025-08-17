# 📡 Realtime Chat App (Laravel + Livewire + Reverb)

A **Realtime Chat Application** built with **Laravel 12** and **Livewire**, powered by **Laravel Reverb** for WebSocket connections.  
It also includes **Realtime Notifications** using Reverb, so users get instant alerts when new messages arrive.

---

## 🚀 Features

- 💬 Realtime chat between users.
- 🔔 Realtime notifications on new messages (via Reverb).
- 🧑‍💻 Built with **Laravel Livewire** for smooth backend–frontend interaction.
- ⚡ Powered by **Laravel Reverb** as a local WebSocket server instead of Pusher.

---

## 🛠️ Requirements

- PHP 8.2+
- Composer
- Node.js + NPM
- Database (MySQL or PostgreSQL)
- Laravel 12
- Reverb (comes with Laravel)

---

## ⚙️ Installation & Setup

1. Clone the project:

   ```bash
   git clone https://github.com/your-username/realtime-chat-app.git
   cd realtime-chat-app
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install && npm run build
   ```

3. Copy the `.env.example` file to `.env` and update your DB and Reverb configs:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations:

   ```bash
   php artisan migrate
   ```

5. Start the Laravel server and Reverb:

   ```bash
   php artisan serve
   php artisan reverb:start
   ```

6. Visit the app in your browser 🚀

---

## 📌 Notes

- Reverb is used for both chat messages and browser notifications.
- Works out-of-the-box without needing third-party services like Pusher.
