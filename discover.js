document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('main-content-container');
    const dislikeButton = document.getElementById('dislike-button');
    const likeButton = document.getElementById('like-button');
    const cards = Array.from(container.querySelectorAll('.profile-card'));
    let currentIndex = 0;

    function showNextProfile() {
        currentIndex++;

        if (currentIndex < cards.length) {
            // Mostramos la siguiente tarjeta
            cards[currentIndex].style.zIndex = 10;
            cards[currentIndex].style.opacity = 1;
        } else {
            showNoProfilesMessage();
        }
    }

    function handleSwipe(direction) {
        if (currentIndex >= cards.length) return;

        const card = cards[currentIndex];
        const translateX = direction === 'like' ? '100%' : '-100%';
        const rotate = direction === 'like' ? '15deg' : '-15deg';

        // Efecto de deslizamiento
        card.style.transform = `translateX(${translateX}) rotate(${rotate})`;
        card.style.opacity = 0;

        setTimeout(() => {
            card.style.display = 'none';
            showNextProfile();
        }, 500);
    }

    function showNoProfilesMessage() {
        dislikeButton.style.display = 'none';
        likeButton.style.display = 'none';

        const noProfilesMessage = document.createElement('div');
        noProfilesMessage.textContent = 'No hay más perfiles disponibles';

        noProfilesMessage.style.position = 'absolute';
        noProfilesMessage.style.top = '50%';
        noProfilesMessage.style.left = '50%';
        noProfilesMessage.style.transform = 'translate(-50%, -50%)';
        noProfilesMessage.style.textAlign = 'center';
        noProfilesMessage.style.fontSize = '1.5rem';
        noProfilesMessage.style.color = 'var(--darkBlue-color)';
        noProfilesMessage.style.fontWeight = 'bold';

        container.appendChild(noProfilesMessage);
    }

    // Eventos de los botones
    dislikeButton.addEventListener('click', () => handleSwipe('dislike'));
    likeButton.addEventListener('click', () => handleSwipe('like'));

    // Mostramos la primera tarjeta
    if (cards.length > 0) {
        cards[0].style.zIndex = 10;
        cards[0].style.opacity = 1;
    }
});
