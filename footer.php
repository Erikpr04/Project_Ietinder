<footer>
    <p><a href="/discover.php">Descubrir</a></p>
    <p><a href="/messages.php">Mensajes</a></p>
    <p><a href="/profile.php">Perfil</a></p>
</footer>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const links = document.querySelectorAll("footer a");
    links.forEach(link => {
        if (window.location.pathname === link.getAttribute("href")) {
            link.classList.add("active");
        }
    });
});

</script>