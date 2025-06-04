document.addEventListener("DOMContentLoaded", function () {
  const faqList = document.querySelector(".faq-list");

  // Toggle FAQ answers
  faqList.addEventListener("click", function (e) {
    const toggleBtn = e.target.closest(".faq-toggle");
    const deleteBtn = e.target.closest(".delete-btn");
    const editBtn = e.target.closest(".edit-btn");
    const saveBtn = e.target.closest(".save-btn");
    const cancelBtn = e.target.closest(".cancel-btn");

    if (toggleBtn) {
      const faqItem = toggleBtn.closest(".faq-item");
      const answer = faqItem.querySelector(".faq-answer");
      const isActive = answer.classList.contains("active");

      // Close all other FAQs first
      document.querySelectorAll(".faq-answer.active").forEach((item) => {
        if (item !== answer) {
          item.classList.remove("active");
          item
            .closest(".faq-item")
            .querySelector(".faq-toggle")
            .classList.remove("active");
        }
      });

      // Toggle current FAQ
      answer.classList.toggle("active");
      toggleBtn.classList.toggle("active");
    }

    if (deleteBtn) {
      const faqItem = deleteBtn.closest(".faq-item");
      if (confirm("Are you sure you want to delete this question?")) {
        faqItem.remove();
      }
    }

    if (editBtn) {
      const faqItem = editBtn.closest(".faq-item");
      const questionView = faqItem.querySelector(".faq-question .view-mode");
      const questionEdit = faqItem.querySelector(".faq-question .edit-mode");
      const answerView = faqItem.querySelector(".faq-answer .view-mode");
      const answerEdit = faqItem.querySelector(".faq-answer .edit-mode");

      // Set current values in edit fields
      const currentQuestion = questionView.querySelector("span").textContent;
      const currentAnswer = answerView.querySelector("p").textContent;
      questionEdit.querySelector("input").value = currentQuestion.trim();
      answerEdit.querySelector("textarea").value = currentAnswer.trim();

      // Switch to edit mode
      questionView.style.display = "none";
      questionEdit.style.display = "block";
      answerView.style.display = "none";
      answerEdit.style.display = "block";
    }

    if (saveBtn) {
      const faqItem = saveBtn.closest(".faq-item");
      const questionInput = faqItem.querySelector(".edit-input");
      const answerTextarea = faqItem.querySelector(".edit-textarea");
      const questionView = faqItem.querySelector(
        ".faq-question .view-mode span"
      );
      const answerView = faqItem.querySelector(".faq-answer .view-mode p");
      const questionViewContainer = faqItem.querySelector(
        ".faq-question .view-mode"
      );
      const questionEditContainer = faqItem.querySelector(
        ".faq-question .edit-mode"
      );
      const answerViewContainer = faqItem.querySelector(
        ".faq-answer .view-mode"
      );
      const answerEditContainer = faqItem.querySelector(
        ".faq-answer .edit-mode"
      );

      // Save changes
      questionView.textContent = questionInput.value;
      answerView.textContent = answerTextarea.value;

      // Switch back to view mode
      questionViewContainer.style.display = "block";
      questionEditContainer.style.display = "none";
      answerViewContainer.style.display = "block";
      answerEditContainer.style.display = "none";
    }

    if (cancelBtn) {
      const faqItem = cancelBtn.closest(".faq-item");
      const questionView = faqItem.querySelector(".faq-question .view-mode");
      const questionEdit = faqItem.querySelector(".faq-question .edit-mode");
      const answerView = faqItem.querySelector(".faq-answer .view-mode");
      const answerEdit = faqItem.querySelector(".faq-answer .edit-mode");

      // Switch back to view mode without saving
      questionView.style.display = "block";
      questionEdit.style.display = "none";
      answerView.style.display = "block";
      answerEdit.style.display = "none";
    }
  });
});
