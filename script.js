// ===============================
// 1. Smooth Scroll (for internal links)
// ===============================
document.documentElement.classList.add("js");
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener("click", function (e) {
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: "smooth" });
    }
  });
});

// ===============================
// 2. Email Form Validation (Hero Section)
// ===============================
const emailForms = document.querySelectorAll(".email-form");

emailForms.forEach(form => {
  form.addEventListener("submit", function (e) {
    const emailInput = form.querySelector("input[type='email']");
    const emailValue = emailInput.value;

    if (!emailValue.includes("@") || !emailValue.includes(".")) {
      alert("Please enter a valid email address.");
      e.preventDefault();
    }
  });
});

// ===============================
// 3. Contact Form Validation
// ===============================
const contactForm = document.querySelector(".contact-section form");

if (contactForm) {
  contactForm.addEventListener("submit", function (e) {

    const name = contactForm.querySelector("input[type='text']").value.trim();
    const email = contactForm.querySelector("input[type='email']").value.trim();
    const phone = contactForm.querySelector("input[type='tel']").value.trim();

    if (name.length < 3) {
      alert("Name must be at least 3 characters.");
      e.preventDefault();
      return;
    }

    if (!email.includes("@")) {
      alert("Enter a valid email address.");
      e.preventDefault();
      return;
    }

    if (phone.length < 10) {
      alert("Enter a valid phone number.");
      e.preventDefault();
      return;
    }

    alert("Form submitted successfully!");
  });
}

// ===============================
// 4. Auto Close Other FAQ Items (Optional Enhancement)
// ===============================
const faqItems = document.querySelectorAll(".faq-item");

faqItems.forEach(item => {
  item.addEventListener("toggle", () => {
    if (item.open) {
      faqItems.forEach(otherItem => {
        if (otherItem !== item) {
          otherItem.removeAttribute("open");
        }
      });
    }
  });
});
document.querySelectorAll('.read-more-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const card = btn.closest('.feature-box');
    card.classList.toggle('active');

    btn.textContent = card.classList.contains('active')
      ? "Show Less"
      : "Read More";
  });
});
// SCROLL ANIMATION
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add("show");
    }
  });
}, { threshold: 0.2 });

document.querySelectorAll('.fade-in, .slide-left, .slide-right, .zoom-in')
  .forEach(el => observer.observe(el));


// OPTIONAL: smooth scroll
document.querySelectorAll("a").forEach(anchor => {
  anchor.addEventListener("click", function(e) {
    if (this.getAttribute("href").startsWith("#")) {
      e.preventDefault();
      document.querySelector(this.getAttribute("href"))
        .scrollIntoView({ behavior: "smooth" });
    }
  });
});