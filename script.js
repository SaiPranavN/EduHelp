
const scrollButton = document.getElementById('scrollButton');
const scrollButton2 = document.getElementById('view_quest_ans');


scrollButton.addEventListener('click', () => {
  // Scroll to the bottom of the page
  window.scrollTo({
    top: 1800,
    behavior: 'smooth' // Smooth scroll animation
  });
});

scrollButton2.addEventListener('click', () => {
    // Scroll to the bottom of the page
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: 'smooth' // Smooth scroll animation
    });
  });