document.addEventListener('DOMContentLoaded', () => {

    //Variables principales
    let currentIndex = 0;
    let cards = [];
    let isLoading = false;
    const searchFilters = [30, 18, 50]; // [Distancia máxima (km), Edad mínima, Edad máxima]

    const container = document.getElementById('main-content-container');
    const dislikeButton = document.getElementById('dislike-button');
    const likeButton = document.getElementById('like-button');
    const menuButton = document.querySelector('.menu-button');
    const menu = document.getElementById('menu-content');
    const filterButton = document.querySelector('.filter-button');
    const slider1 = document.getElementById('slider1');
    const slider1ValueLabel = document.getElementById('slider1-value');
    const slider1MinValue = document.getElementById('slider1-min');
    const slider1MaxValue = document.getElementById('slider1-max');
    
    // Obtén los sliders de rango correctamente
    const slider2 = document.querySelectorAll('.range-slider input[type="range"]');
    let slider2_1 = parseInt(slider2[0]?.value);
    let slider2_2 = parseInt(slider2[1]?.value );

    if (menu) {
        // Mostrar los valores iniciales de slider1
        slider1.value = searchFilters[0];
        slider1ValueLabel.textContent = `${slider1.value} km`;
        slider1MinValue.textContent = `${slider1.min}`;
        slider1MaxValue.textContent = `${slider1.max}`;

        // Actualizar el valor del slider1 al moverlo
        slider1.addEventListener('input', () => {
            slider1ValueLabel.textContent = `${slider1.value} km`;
            searchFilters[0] = slider1.value;
        });

        function updateSlider2Values() {
            slider2_1 = parseFloat(slider2[0]?.value );
            slider2_2 = parseFloat(slider2[1]?.value );

            // Asegurar que los valores estén en el orden correcto
            if (slider2_1 > slider2_2) {
                [slider2_1, slider2_2] = [slider2_2, slider2_1];
            }

            searchFilters[1] = slider2_1; // Edad mínima
            searchFilters[2] = slider2_2; // Edad máxima

            // Mostrar los valores actualizados
            const displayElement = document.querySelector('.rangeValues'); 
            if (displayElement) {
                displayElement.textContent = `${slider2_1} - ${slider2_2}`;
            }
        }

        slider2.forEach(slider => {
            slider.addEventListener('input', updateSlider2Values);
        });

        // Mostrar los valores iniciales de slider2
        updateSlider2Values();

        // Funciones del menú
        function toggleMenu() {
            menu.classList.toggle('active');
            menuButton.classList.toggle('rotated');
        }

        function applyFilter() {
            deleteCardsArray();
            loadProfiles();
        }

        // Eventos del menú
        menuButton.addEventListener('click', (event) => {
            event.stopPropagation();
            toggleMenu();
        });

        filterButton.addEventListener('click', (event) => {
            event.stopPropagation();
            applyFilter();
        });
    } else {
        console.error('Uno o más elementos no se han encontrado en el DOM. Verifica que todos los elementos están correctamente asignados.');
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function loadProfiles() {
        if (isLoading) return;
        isLoading = true;
        console.log("Search Filters: ",searchFilters[1]);
        console.log("Search Filters: ",searchFilters[2]);

    
        $.ajax({
            url: 'rsc/getDiscoverData.php',
            method: 'GET',
            dataType: 'json',
            data: jQuery.param({ maxDistance: searchFilters[0], maxAge : searchFilters[1],minAge: searchFilters[2]}) ,
            success: function(data) {
                if (data.success) {
                    console.log("Showing buttons...");                  
                    const newProfilesContainer = document.createElement('div');
                    newProfilesContainer.className = 'new-profile-content';
                    newProfilesContainer.innerHTML = data.html;
    
                    container.prepend(newProfilesContainer);
    
                    $(newProfilesContainer).find('.carousel').each(function() {
                        initializeCarousel(this);
                    });

                    updateCardsArray();
                    currentIndex = 0;

                    if (currentIndex === 0) {
                        showCard(0);
                    }
    
                } else {
                    console.error('Error loading profiles:', data.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    console.error('Server error:', errorData.error);
                } catch (e) {
                    console.error('Could not parse error response:', xhr.responseText);
                }
            },
            complete: function() {
                isLoading = false;
            }
        });
    }
    

    function updateCardsArray() {
        $(dislikeButton).show();
        $(likeButton).show();  
        cards = Array.from(container.querySelectorAll('.profile-card'));
        cards.forEach((card, index) => {
            card.style.display = 'none';
            card.style.transform = 'none';
            card.style.opacity = '0';
            card.style.zIndex = cards.length - index;
        });
    }

    function deleteCardsArray(){
        $('.new-profile-content').remove();
            cards.forEach(card => {
                card.remove();
            });
            cards = [];
        
    }

    function showCard(index) {
        if (index >= cards.length || cards == []) {
            $(dislikeButton).hide();
            $(likeButton).hide();
            console.log("No more profiles to show")
            showNoProfilesMessage();
            return;
        }

        cards.forEach((card, i) => {
            if (i === index) {
                card.style.display = 'block';
                card.style.opacity = '1';
                card.style.transform = 'none';
            } else {
                card.style.display = 'none';
            }
        });
    }

    async function handleSwipe(direction) {
        if (currentIndex >= cards.length) return;

        const card = cards[currentIndex];
        const translateX = direction === 'like' ? '100%' : '-100%';
        const rotate = direction === 'like' ? '15deg' : '-15deg';

        card.style.transform = `translateX(${translateX}) rotate(${rotate})`;
        card.style.opacity = '0';
        card.style.transition = 'transform 0.3s ease, opacity 0.3s ease';

        if (direction === 'like') {
             processLikeInteraction(card);
        }

        
        setTimeout(() => {
            card.remove();
            currentIndex++;
            showCard(currentIndex);
        }, 300);


    }

    function processLikeInteraction(card) {
        const user1_id = getCookie('user_id');
        const user2_id = card.getAttribute('data-user-id');

        if (!user1_id || !user2_id || isNaN(user2_id)) {
            console.error("Invalid user IDs:", { user1_id, user2_id });
            return;
        }

        fetch('/rsc/handleLike.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                user1_id: parseInt(user1_id),
                user2_id: parseInt(user2_id),
            }),
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.match) {
                showMatchNotification();
            }
            return data;
        })
        .catch(function(error) {
            console.error('Error processing like:', error);
        });
        
        
    }


    function showMatchNotification() {
        const overlay = document.getElementById('matchOverlay');
        overlay.style.display = 'flex';

        document.getElementById('continueButton').addEventListener('click', () => {
            overlay.style.display = 'none';
        });

        document.getElementById('messagesButton').addEventListener('click', () => {
            window.location.href = '/messages.php';
        });
    }

    function showNoProfilesMessage() {
        $(dislikeButton).hide();
        $(likeButton).hide();
        
        const noProfilesMessage = document.createElement('div');
        noProfilesMessage.className = 'no-profiles-message';
        noProfilesMessage.textContent = 'No hay más perfiles disponibles';
        noProfilesMessage.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 1.5rem;
            color: var(--darkBlue-color);
            font-weight: bold;
        `;
        
        container.appendChild(noProfilesMessage);
    }
    function initializeCarousel(carousel) {
        const images = carousel.querySelectorAll('.carousel-image');
        const indicators = carousel.querySelectorAll('.indicator');
        let currentImageIndex = 0;
    
        function changeImage(index) {
            if (index < 0) index = images.length - 1;
            if (index >= images.length) index = 0;
    
            images[currentImageIndex].classList.remove('active');
            indicators[currentImageIndex].classList.remove('active');
            
            currentImageIndex = index;
            
            images[currentImageIndex].classList.add('active');
            indicators[currentImageIndex].classList.add('active');
        }
    
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', (e) => {
                e.stopPropagation();
                changeImage(index);
            });
        });
    
        carousel.addEventListener('click', () => {
            changeImage(currentImageIndex + 1);
        });
    
        let touchStartX = 0;
        let touchEndX = 0;
    
        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
    
        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipeGesture();
        });
    
        function handleSwipeGesture() {
            if (touchStartX - touchEndX > 50) {
                changeImage(currentImageIndex + 1);
            } else if (touchEndX - touchStartX > 50) {
                changeImage(currentImageIndex - 1);
            }
        }
    }

    dislikeButton.addEventListener('click', () => handleSwipe('dislike'));
    likeButton.addEventListener('click', () => handleSwipe('like'));

    loadProfiles();
});
