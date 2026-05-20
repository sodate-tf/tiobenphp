(() => {
  const btns = document.querySelectorAll("[data-story-href]");
  btns.forEach((b) => {
    b.addEventListener("click", () => {
      const href = b.getAttribute("data-story-href");
      if (href) window.open(href, "_blank", "noopener");
    });
  });
})();
