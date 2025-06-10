const roleSection = document.getElementById("role");
const userSection = document.getElementById("jobseeker");
const companySection = document.getElementById("company");

const roleCards = document.querySelectorAll(".role-card");
const continueBtn = document.querySelector(".continue-btn");
const textRegister = document.getElementById("text-register");
let selectedRole = null;

roleCards.forEach((card) => {
  card.addEventListener("click", () => {
    roleCards.forEach((c) => c.classList.remove("selected"));
    card.classList.add("selected");
    selectedRole = card.dataset.role;
    continueBtn.classList.add("active");
  });
});

continueBtn.addEventListener("click", () => {
  if (selectedRole) {
    if (selectedRole === "jobseeker") {
      roleSection.style.display = "none";
      userSection.style.display = "block";
      textRegister.innerText =
        "Silakan lengkapi data diri Anda sebagai Pencari Kerja.";
    } else if (selectedRole === "company") {
      roleSection.style.display = "none";
      companySection.style.display = "block";
      textRegister.innerText = "Silakan lengkapi data perusahaan Anda.";
    }
  }
});
