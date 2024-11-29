<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Risk Management System - Documentation</title>
   <style>
       * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
           font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
       }

       body {
           line-height: 1.6;
           color: #24292e;
           background: #f6f8fa;
           padding: 40px 20px;
       }

       .container {
           max-width: 1000px;
           margin: 0 auto;
           background: white;
           padding: 40px;
           border-radius: 10px;
           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
       }

       .header {
           text-align: center;
           margin-bottom: 40px;
       }

       h1 {
           font-size: 2.5em;
           color: #1a3c40;
           margin-bottom: 10px;
       }

       .subtitle {
           color: #586069;
           font-size: 1.2em;
       }

       h2 {
           color: #1a3c40;
           margin: 30px 0 15px;
           padding-bottom: 10px;
           border-bottom: 2px solid #eaecef;
       }

       .features-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
           gap: 20px;
           margin: 20px 0;
       }

       .feature-card {
           background: #f8f9fa;
           padding: 20px;
           border-radius: 8px;
           border-left: 4px solid #1a3c40;
       }

       .faculty-list {
           list-style: none;
           margin: 20px 0;
       }

       .faculty-list li {
           padding: 10px 15px;
           background: #f8f9fa;
           margin-bottom: 8px;
           border-radius: 4px;
           border-left: 4px solid #1a3c40;
       }

       .tech-stack {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
           gap: 15px;
           margin: 20px 0;
       }

       .tech-item {
           background: #f8f9fa;
           padding: 15px;
           border-radius: 8px;
           text-align: center;
           font-weight: 500;
       }

       table {
           width: 100%;
           border-collapse: collapse;
           margin: 20px 0;
       }

       th, td {
           padding: 12px 15px;
           text-align: left;
           border-bottom: 1px solid #eaecef;
       }

       th {
           background: #f8f9fa;
           font-weight: 600;
       }

       .progress-list {
           list-style: none;
           margin: 20px 0;
       }

       .progress-item {
           display: flex;
           align-items: center;
           margin-bottom: 10px;
       }

       .progress-item input[type="checkbox"] {
           margin-right: 10px;
       }

       code {
           display: block;
           background: #f6f8fa;
           padding: 15px;
           border-radius: 6px;
           margin: 15px 0;
           font-family: monospace;
       }

       .footer {
           text-align: center;
           margin-top: 40px;
           padding-top: 20px;
           border-top: 2px solid #eaecef;
           color: #586069;
       }

       .contact-links {
           display: flex;
           justify-content: center;
           gap: 20px;
           margin: 20px 0;
       }

       .contact-links a {
           color: #1a3c40;
           text-decoration: none;
           padding: 8px 15px;
           border-radius: 4px;
           background: #f8f9fa;
       }

       .contact-links a:hover {
           background: #eaecef;
       }
   </style>
</head>
<body>
   <div class="container">
       <div class="header">
           <h1>🚀 Risk Management System</h1>
           <p class="subtitle">UIN Sunan Kalijaga Yogyakarta</p>
       </div>

       <p>Sebuah sistem manajemen risiko berbasis web untuk memantau dan mengelola risiko di 8 fakultas UIN Sunan Kalijaga Yogyakarta. Project ini dikembangkan untuk memenuhi tugas UAS mata kuliah Bahasa-Bahasa Pemrograman.</p>

       <h2>🎯 Features</h2>
       <div class="features-grid">
           <div class="feature-card">Multi-level user authentication (Admin & Fakultas)</div>
           <div class="feature-card">Dashboard interaktif untuk monitoring risiko</div>
           <div class="feature-card">Sistem penilaian risiko otomatis</div>
           <div class="feature-card">Manajemen mitigasi risiko</div>
           <div class="feature-card">Pelaporan dan visualisasi data</div>
           <div class="feature-card">Interface responsif dan modern</div>
       </div>

       <h2>🏢 Supported Faculties</h2>
       <ul class="faculty-list">
           <li>Fakultas Adab dan Ilmu Budaya</li>
           <li>Fakultas Dakwah dan Komunikasi</li>
           <li>Fakultas Ekonomi dan Bisnis Islam</li>
           <li>Fakultas Ilmu Sosial dan Humaniora</li>
           <li>Fakultas Ilmu Tarbiyah dan Keguruan</li>
           <li>Fakultas Syariah dan Hukum</li>
           <li>Fakultas Sains dan Teknologi</li>
           <li>Fakultas Ushuluddin dan Pemikiran Islam</li>
       </ul>

       <h2>🛠️ Tech Stack</h2>
       <div class="tech-stack">
           <div class="tech-item">HTML5</div>
           <div class="tech-item">CSS3</div>
           <div class="tech-item">JavaScript</div>
           <div class="tech-item">PHP 8.2</div>
           <div class="tech-item">MariaDB 11.32</div>
           <div class="tech-item">Apache2</div>
       </div>

       <h2>👥 Team Members</h2>
       <table>
           <tr>
               <th>Name</th>
               <th>Role</th>
               <th>Responsibility</th>
           </tr>
           <tr>
               <td>Dipta</td>
               <td>Backend Lead</td>
               <td>Database & API Development</td>
           </tr>
           <tr>
               <td>Agung</td>
               <td>UI/UX</td>
               <td>Frontend Design & Implementation</td>
           </tr>
           <tr>
               <td>Bayu</td>
               <td>Frontend</td>
               <td>Logic & Integration</td>
           </tr>
           <tr>
               <td>Faris & Rafli</td>
               <td>Support</td>
               <td>Documentation & Testing</td>
           </tr>
       </table>

       <h2>📋 Project Status</h2>
       <ul class="progress-list">
           <li class="progress-item">
               <input type="checkbox" checked disabled> Database Design
           </li>
           <li class="progress-item">
               <input type="checkbox" checked disabled> User Authentication
           </li>
           <li class="progress-item">
               <input type="checkbox" checked disabled> Basic Dashboard
           </li>
           <li class="progress-item">
               <input type="checkbox" disabled> Risk Assessment Module
           </li>
           <li class="progress-item">
               <input type="checkbox" disabled> Reporting System
           </li>
           <li class="progress-item">
               <input type="checkbox" disabled> Faculty Integration
           </li>
           <li class="progress-item">
               <input type="checkbox" disabled> Testing & Documentation
           </li>
           <li class="progress-item">
               <input type="checkbox" disabled> Deployment
           </li>
       </ul>

       <h2>🚀 Getting Started</h2>
       <code>
# Clone repository
git clone https://github.com/yourusername/risk-management.git

# Setup database
Import SQL file from database/init.sql

# Configure database connection
Edit config/database.php with your credentials

# Run development server
php -S localhost:8000
       </code>

       <div class="contact-links">
           <a href="https://github.com/yourusername/risk-management">📂 Repository</a>
           <a href="https://rbwtech.io/bbp">🌐 Live Demo</a>
           <a href="mailto:your@email.com">📧 Contact</a>
       </div>

       <div class="footer">
           <p>Made with ❤️ by Team BBP UIN Sunan Kalijaga</p>
       </div>
   </div>
</body>
</html>
