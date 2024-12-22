document.addEventListener("DOMContentLoaded", () => {
  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");

  document.body.appendChild(canvas);

  canvas.style.position = "absolute";
  canvas.style.top = "0";
  canvas.style.left = "0";
  canvas.style.zIndex = "-1";
  canvas.style.width = "100%";
  canvas.style.height = "100%";

  const resizeCanvas = () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
  };

  resizeCanvas();
  window.addEventListener("resize", resizeCanvas);

  const particles = [];
  const colors = ["#0B4F6C", "#01BAEF", "#32936F", "#A2C3A4", "#F8C41B", "#C76B98"];
  const maxParticles = 120;
  const connectionDistance = 150;

  const createParticle = () => {
      return {
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
          radius: Math.random() * 4 + 2,
          color: colors[Math.floor(Math.random() * colors.length)],
          speedX: Math.random() * 1.5 - 0.75,
          speedY: Math.random() * 1.5 - 0.75,
          alpha: Math.random() * 0.6 + 0.4,
      };
  };

  for (let i = 0; i < maxParticles; i++) {
      particles.push(createParticle());
  }

  const drawParticle = (particle) => {
      ctx.beginPath();
      ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(${hexToRgb(particle.color)}, ${particle.alpha})`;
      ctx.shadowColor = particle.color;
      ctx.shadowBlur = 20;
      ctx.fill();
      ctx.closePath();
  };

  const updateParticle = (particle) => {
      particle.x += particle.speedX;
      particle.y += particle.speedY;

      if (particle.x < 0 || particle.x > canvas.width) particle.speedX *= -1;
      if (particle.y < 0 || particle.y > canvas.height) particle.speedY *= -1;
  };

  const connectParticles = () => {
      for (let i = 0; i < particles.length; i++) {
          for (let j = i + 1; j < particles.length; j++) {
              const dx = particles[i].x - particles[j].x;
              const dy = particles[i].y - particles[j].y;
              const distance = Math.sqrt(dx * dx + dy * dy);

              if (distance < connectionDistance) {
                  const alpha = 1 - distance / connectionDistance;
                  ctx.beginPath();
                  ctx.moveTo(particles[i].x, particles[i].y);
                  ctx.lineTo(particles[j].x, particles[j].y);
                  ctx.strokeStyle = `rgba(255, 255, 255, ${alpha})`;
                  ctx.lineWidth = 1;
                  ctx.stroke();
                  ctx.closePath();
              }
          }
      }
  };

  const animate = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      particles.forEach((particle) => {
          drawParticle(particle);
          updateParticle(particle);
      });

      connectParticles();

      requestAnimationFrame(animate);
  };

  animate();

  function hexToRgb(hex) {
      const bigint = parseInt(hex.slice(1), 16);
      const r = (bigint >> 16) & 255;
      const g = (bigint >> 8) & 255;
      const b = bigint & 255;
      return `${r}, ${g}, ${b}`;
  }
});
