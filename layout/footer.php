<footer class="footer">
  <div class="footer-content">


    <div class="thanks-section">
      <h2>Terima Kasih</h2>
      <p>Atas partisipasi Anda</p>
    </div>

    <div class="footer-contact">
      <div class="contact-cards">
        <a href="https://ekahusada.com/" target="_blank" class="contact-card">
          <div class="card-icon">
            <i class="fas fa-globe"></i>
          </div>
          <div class="card-info">
            <h4>Website</h4>
          </div>
        </a>

        <a href="https://www.google.com/maps/place/Rumah+Sakit+Eka+Husada" target="_blank" class="contact-card">
          <div class="card-icon">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <div class="card-info">
            <h4>Lokasi</h4>
          </div>
        </a>

        <a href="https://api.whatsapp.com/send/?phone=082244125457" target="_blank" class="contact-card">
          <div class="card-icon whatsapp">
            <i class="fab fa-whatsapp"></i>
          </div>
          <div class="card-info">
            <h4>WA Pengaduan</h4>
          </div>
        </a>
      </div>
    </div>


    <div class="footer-bottom">
      <p>RS Eka Husada - Melayani Dengan Sepenuh Hati</p>
    </div>

  </div>
</footer>

<style>
  .footer {
    background: linear-gradient(135deg, #0ea5e9 0%, #14b8a6 100%);
    color: white;
    padding: 25px 20px 20px;
    margin-top: 40px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: relative;
    overflow: hidden;
  }

  .footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.3;
    pointer-events: none;
  }

  .footer-content {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
  }


  .thanks-section {
    text-align: center;
    padding-bottom: 7px;
    margin-bottom: 7px;
  }

  .thanks-icon {
    width: 36px;
    height: 36px;
    margin: 0 auto 10px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {

    0%,
    100% {
      transform: scale(1);
      opacity: 1;
    }

    50% {
      transform: scale(1.05);
      opacity: 0.9;
    }
  }

  .thanks-section h2 {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 4px;
    color: white;
    letter-spacing: -0.3px;
  }

  .thanks-section p {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
  }


  .contact-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 15px;
  }

  .contact-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 10px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    text-decoration: none;
    color: white;
    transition: all 0.2s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .contact-card:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .contact-card:active {
    transform: scale(0.98);
  }

  .card-icon {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  .card-icon.whatsapp {
    background: #25D366;
    color: white;
  }

  .card-info h4 {
    font-size: 12px;
    font-weight: 600;
    margin: 0;
    color: white;
    white-space: nowrap;
  }


  .footer-bottom {
    text-align: center;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
  }

  .footer-bottom p {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.95);
    margin: 0;
    font-weight: 500;
  }

  @media (max-width: 640px) {
    .footer {
      padding: 20px 15px 15px;
      margin-top: 30px;
    }

    .thanks-section {
      padding-bottom: 12px;
      margin-bottom: 12px;
    }

    .thanks-icon {
      width: 32px;
      height: 32px;
      margin-bottom: 8px;
    }

    .thanks-icon svg {
      width: 16px;
      height: 16px;
    }

    .thanks-section h2 {
      font-size: 16px;
    }

    .thanks-section p {
      font-size: 12px;
    }

    .contact-cards {
      grid-template-columns: 1fr;
      gap: 8px;
    }

    .contact-card {
      padding: 10px 12px;
      justify-content: flex-start;
    }

    .card-icon {
      width: 28px;
      height: 28px;
      font-size: 13px;
    }

    .card-info h4 {
      font-size: 13px;
    }

    .footer-bottom {
      padding-top: 12px;
    }

    .footer-bottom p {
      font-size: 11px;
    }
  }


  @media (max-width: 380px) {
    .footer {
      padding: 18px 12px 12px;
    }

    .thanks-section h2 {
      font-size: 15px;
    }

    .thanks-section p {
      font-size: 11px;
    }

    .contact-card {
      padding: 9px 10px;
    }

    .card-icon {
      width: 26px;
      height: 26px;
      font-size: 12px;
    }

    .card-info h4 {
      font-size: 12px;
    }

    .footer-bottom p {
      font-size: 10px;
    }
  }

  @media (min-width: 641px) and (max-width: 1024px) {
    .contact-cards {
      gap: 12px;
    }
  }


  @media print {
    .footer {
      background: #fff !important;
      color: #000 !important;
      padding: 15px;
      margin-top: 20px;
    }

    .thanks-icon,
    .card-icon {
      display: none;
    }

    .contact-card {
      background: #f1f5f9 !important;
      color: #000 !important;
      border: 1px solid #ccc;
    }
  }
</style>