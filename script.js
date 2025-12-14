document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleBtn");

    if (!sidebar || !toggleBtn) return;

    toggleBtn.addEventListener("click", () => {
        // Mobile
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("open");
            document.body.classList.toggle("no-scroll");
        } 
        // Desktop
        else {
            sidebar.classList.toggle("collapsed");
        }
    });

    // Fecha sidebar ao clicar fora (mobile)
    document.addEventListener("click", (e) => {
        if (
            window.innerWidth <= 768 &&
            sidebar.classList.contains("open") &&
            !sidebar.contains(e.target) &&
            !toggleBtn.contains(e.target)
        ) {
            sidebar.classList.remove("open");
            document.body.classList.remove("no-scroll");
        }
    });

    // Corrige estado ao redimensionar tela
    window.addEventListener("resize", () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("open");
            document.body.classList.remove("no-scroll");
        }
    });
});
