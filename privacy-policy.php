<?php include './includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Privacy Policy - Khair ul Quran Academy</title>
<style>
    :root {
      --primary-color:rgb(156, 116, 92); /* Vibrant orange */
      --secondary-color:rgb(0, 0, 0); /* Deep blue */
      --accent-color:#dcd6d1; /* Bright yellow */
      --dark-text:rgb(249, 246, 243); /* Almost black */
      --light-bg:rgb(219, 201, 201); /* Pure white */
      --highlight-color: #695444 ; /* Off-white */
      --brown-accent:rgb(248, 248, 248); /* Rich brown */
    }
    
    
    .privacy-container {
      max-width: 900px;
      margin: 3rem auto;
      padding: 3rem;
      box-shadow: 0 5px 25px rgba(0,0,0,0.08);
      border-radius: 12px;
      background-color: var(--light-bg);
    }
    
    .privacy-container h1 {
      color: var(--primary-color);
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2.5rem;
      font-weight: 700;
      position: relative;
      padding-bottom: 1.5rem;
    }
    
    .privacy-container h1:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 120px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-color), var(--brown-accent));
      border-radius: 2px;
    }
    
    .privacy-container h2 {
      color: var(--secondary-color);
      margin: 2.5rem 0 1.5rem;
      font-size: 1.6rem;
      font-weight: 600;
      position: relative;
      padding-left: 1.2rem;
    }
    
    .privacy-container h2:before {
      content: '';
      position: absolute;
      left: 0;
      top: 0.5rem;
      height: 1.8rem;
      width: 6px;
      background-color: var(--primary-color);
      border-radius: 3px;
    }
    
    .privacy-container p {
      margin-bottom: 1.5rem;
      font-size: 1.05rem;
    }
    
    .privacy-container ul {
      margin: 1.5rem 0 2rem 2rem;
      padding-left: 1rem;
    }
    
    .privacy-container li {
      margin-bottom: 1rem;
      position: relative;
      padding-left: 1.5rem;
    }
    
    
    .highlight-box {
      background-color: var(--highlight-color);
      border-left: 4px solid var(--primary-color);
      padding: 1.8rem;
      margin: 2.5rem 0;
      border-radius: 0 8px 8px 0;
      font-size: 1.1rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    
    .contact-info-bg {
      background: linear-gradient(135deg, var(--highlight-color), var(--light-bg));
      padding: 2rem;
      border-radius: 10px;
      margin-top: 3rem;
      border: 1px solid rgba(0,0,0,0.05);
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .contact-info-bg h2 {
      color: var(--brown-accent);
      margin-top: 0;
    }
    
    .contact-info-bg a {
      color: var(--secondary-color);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
    }
    
    .contact-info-bg a:hover {
      color: var(--primary-color);
      text-decoration: underline;
    }
    
    .effective-date {
      background-color: var(--primary-color);
      color: white;
      display: inline-block;
      padding: 0.5rem 1.2rem;
      border-radius: 30px;
      font-weight: 600;
      margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
      .privacy-container {
        padding: 1.5rem;
        margin: 1.5rem;
      }
      
      .privacy-container h1 {
        font-size: 2rem;
      }
      
      .highlight-box {
        padding: 1.2rem;
      }
    }
</style>
</head>
<body>
  <section class="privacy-container">
    <h1>Privacy Policy</h1>
    <div class="effective-date">Effective: April 12, 2025</div>
    
    <div class="highlight-box">
      At <strong>Khair ul Quran Academy</strong>, we prioritize your privacy with the highest Islamic ethical standards. This comprehensive policy details our commitment to protecting your personal information while delivering exceptional Quranic education.
    </div>

    <h2>1. Information Collection</h2>
    <p>We responsibly gather only necessary information to serve you better:</p>
    <ul>
      <li><strong>Personal Details:</strong> Full name, email, phone number, location, student age/grade level, and other relevant registration information.</li>
      <li><strong>Technical Data:</strong> IP address, device information, browser type, and usage patterns to enhance your experience.</li>
    </ul>

    <h2>2. Purpose of Data Processing</h2>
    <p>Your information enables us to:</p>
    <ul>
      <li>Provide personalized Quranic education services and support</li>
      <li>Process enrollments and manage student accounts efficiently</li>
      <li>Communicate important updates, educational content, and service announcements</li>
      <li>Continuously improve our teaching methodologies and digital platforms</li>
    </ul>

    <h2>3. Robust Data Protection</h2>
    <p>We implement enterprise-grade security measures:</p>
    <ul>
      <li>End-to-end encryption for sensitive data transmissions</li>
      <li>Regular security assessments and penetration testing</li>
      <li>Strict access controls with multi-factor authentication</li>
      <li>Comprehensive staff training on data protection</li>
    </ul>
    <p>While we employ industry best practices, we recommend users maintain strong passwords and device security.</p>

    <h2>4. Responsible Data Sharing</h2>
    <p>We maintain transparency about information sharing:</p>
    <ul>
      <li>Payment processors with PCI-DSS compliance for financial transactions</li>
      <li>Educational technology partners under strict data processing agreements</li>
      <li>When legally mandated by regulatory authorities</li>
    </ul>
    <p>We never sell or rent your personal information to third parties.</p>

    <h2>5. Digital Technologies</h2>
    <p>Our technology implementation includes:</p>
    <ul>
      <li>Essential cookies for platform functionality</li>
      <li>Analytics to understand usage patterns and improve services</li>
      <li>Secure session management for uninterrupted learning</li>
    </ul>
    <p>You maintain full control through browser settings and our cookie preference center.</p>

    <h2>6. Your Data Rights</h2>
    <p>In accordance with global privacy standards, you can:</p>
    <ul>
      <li>Access your complete data profile</li>
      <li>Request rectification of inaccurate information</li>
      <li>Withdraw consent for specific processing activities</li>
      <li>Request secure deletion where applicable</li>
      <li>Obtain your data in portable format</li>
    </ul>

    <h2>7. External Resources</h2>
    <p>While we carefully select external resources, we recommend reviewing third-party privacy policies when leaving our platform.</p>

    <h2>8. Youth Protection</h2>
    <p>We implement special safeguards for students under 18, including:</p>
    <ul>
      <li>Parental consent requirements for minors</li>
      <li>Age-appropriate content filtering</li>
      <li>Restricted data collection for younger users</li>
    </ul>

    <h2>9. Policy Evolution</h2>
    <p>This living document is regularly reviewed and updated. Significant changes will be communicated through our official channels.</p>

    <div class="contact-info-bg">
      <h2>10. Connect With Us</h2>
      <p>For privacy inquiries or to exercise your rights:</p>
      <p>
        <strong>Email:</strong> <a href="mailto:khairulonline@gmail.com">khairulonline@gmail.com</a><br>
        <strong>Phone:</strong> <a href="tel:+923140119953">+92 314 0119953</a><br>
        <strong>Office:</strong> Shah Faisal Colony, Shamsi Society, Karachi, Pakistan<br>
        <strong>Hours:</strong> 9:00 AM - 5:00 PM (PKT), Monday-Friday
      </p>
      <p>We typically respond to inquiries within 24-48 business hours.</p>
    </div>
  </section>
  
  <?php include './includes/footer.php'; ?>
</body>
</html>