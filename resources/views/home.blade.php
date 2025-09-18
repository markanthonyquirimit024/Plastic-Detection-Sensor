@include('layout.shopping-base')

  <title>EcoScan – Smart Plastic Detection</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      color: #fff;
      background: linear-gradient(135deg, #0d0d0d, #1a3d1f);
    }

    /* Hero Section */
    .hero {
        margin-top: 50px;
      padding: 100px 20px;
      background: radial-gradient(circle, rgba(25,121,54,0.9) 0%, rgba(0,0,0,0.9) 100%);
    }
    .hero .row {
      align-items: center;
    }
    .hero-text h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .hero-text p {
      font-size: 1.2rem;
      color: #ccc;
      margin-bottom: 30px;
    }
    .hero-text .btn-primary {
      padding: 14px 32px;
      font-size: 1.1rem;
      border-radius: 50px;
      background: linear-gradient(90deg, #4caf50, #2f6d2a);
      border: none;
    }
    .hero-img img {
      max-width: 60%;
      border-radius: 18px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.6);
    }

    /* Features */
    .features {
      padding: 80px 20px;
      background: rgba(255,255,255,0.05);
    }
    .features h2 {
      text-align: center;
      margin-bottom: 50px;
      font-weight: 600;
    }
    .feature-card {
      background: rgba(255,255,255,0.08);
      border-radius: 15px;
      padding: 30px;
      text-align: center;
      transition: transform 0.3s ease;
      backdrop-filter: blur(8px);
    }
    .feature-card:hover {
      transform: translateY(-8px);
    }
    .feature-card h4 {
      margin-top: 15px;
      font-size: 1.3rem;
      font-weight: 600;
    }
    .feature-card p {
      color: #bbb;
      font-size: 0.95rem;
      margin-top: 10px;
    }

    /* CTA */
    .cta {
      padding: 80px 20px;
      text-align: center;
    }
    .cta h2 {
      font-size: 2rem;
      margin-bottom: 20px;
    }
    .cta p {
      max-width: 600px;
      margin: 0 auto 30px;
      color: #ccc;
    }
    .cta .btn {
      padding: 12px 28px;
      border-radius: 50px;
      font-weight: bold;
    }

    /* Footer */
    footer {
      padding: 30px 20px;
      text-align: center;
      color: #aaa;
      font-size: 0.9rem;
      background: #0d0d0d;
    }
  </style>

  <!-- Hero Section -->
  <section class="hero container">
    <div class="row g-5">
      <div class="col-md-6 hero-img text-center">
        <img src="{{ asset('images/slide5.jpg') }}" alt="EcoScan Sensor">
      </div>

      <div class="col-md-6 hero-text">
        <h1>🌍 EcoScan - Smart Plastic Detection</h1>
        <p>Empowering communities and businesses to identify, track, and reduce single-use plastics with AI-powered detection and analytics.</p>
        <a href="#features" class="btn btn-primary">Learn More</a>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section id="features" class="features container">
    <h2>Key Features</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card">
          <div>📊</div>
          <h4>Real-time Analytics</h4>
          <p>Track plastic vs non-plastic detections in real time with interactive dashboards.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div>🤖</div>
          <h4>AI-Powered Detection</h4>
          <p>Leverage advanced machine learning models for accurate material classification.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div>🔒</div>
          <h4>Secure & Scalable</h4>
          <p>Built with modern security and scalability in mind to handle growing data needs.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta">
    <h2>Ready to Take Action?</h2>
    <p>Join the movement towards a plastic-free environment. Discover insights, generate reports, and help reduce single-use plastic waste.</p>
    <a href="{{route('register')}}" class="btn btn-success">Get Started</a>
  </section>

  <!-- Footer -->
  <footer>
    &copy; 2025 EcoScan. All rights reserved.
  </footer>
