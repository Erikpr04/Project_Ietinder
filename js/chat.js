document.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.getElementById('chatContainer');
    const urlParams = new URLSearchParams(window.location.search);
    const conversationId = urlParams.get('conversation_id');
    const userId = getCookie('user_id'); // Función para obtener el user_id de las cookies
    let lastMessageId = null; // Almacena el ID del último mensaje cargado
    let lastMessageTimestamp = null; // Para rastrear la fecha del último mensaje
    let isSendingMessage = false; // Controlar si estamos enviando un mensaje para evitar cargarlo dos veces

    if (!conversationId || !userId) {
        chatContainer.innerHTML = '<p>Error: No se pudo cargar la conversación.</p>';
        return;
    }

    // Función para cargar mensajes nuevos
    function loadNewMessages() {
        if (isSendingMessage) {
            // Si estamos enviando un mensaje, no cargamos nuevos mensajes
            return;
        }

        $.ajax({
            url: `rsc/getMessages.php?conversation_id=${conversationId}&last_message_id=${lastMessageId || 0}`,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    appendMessages(data.messages);
                } else {
                    console.error('Error loading messages:', data.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX request failed:', { status, error, response: xhr.responseText });
            }
        });
    }

    function appendMessages(messages) {
        messages.forEach(message => {
            const messageTimestamp = new Date(message.timestamp); // Asegúrate de que los mensajes incluyan un campo "timestamp" en formato ISO
            const timeDifference = lastMessageTimestamp 
                ? (messageTimestamp - lastMessageTimestamp) / (1000 * 60) 
                : null;

            // Agregar separador si han pasado más de 5 minutos o si es el primer mensaje
            if (!lastMessageTimestamp || timeDifference > 5) {
                const separator = document.createElement('div');
                separator.className = 'date-separator';
                separator.textContent = formatDate(messageTimestamp);
                chatContainer.appendChild(separator);
            }

            // Crear el contenedor del mensaje
            const messageDiv = document.createElement('div');
            messageDiv.className = message.sender_id === parseInt(userId) 
                ? 'message my-message' 
                : 'message their-message';

            messageDiv.innerHTML = ` 
                ${message.sender_id !== parseInt(userId) ? `<img class="profile-image-chat" src="${message.sender_photo || 'default.jpg'}">` : ''} 
                <p>${message.content}</p>
            `;

            chatContainer.appendChild(messageDiv);

            // Actualizar el timestamp del último mensaje procesado
            lastMessageTimestamp = messageTimestamp;
        });

        // Actualiza el ID del último mensaje
        if (messages.length > 0) {
            lastMessageId = messages[messages.length - 1].message_id;
        }

        // Desplaza hacia abajo automáticamente
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Función para formatear la fecha en estilo "Martes, 14 Enero 2025, 10:39"
    function formatDate(date) {
        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        const dayName = days[date.getDay()];
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
    
        return `${dayName}, ${day} ${month} ${year}, ${hours}:${minutes}`;
    }

    // Cargar mensajes iniciales
    loadNewMessages();

    // Consulta periódica para nuevos mensajes
    setInterval(loadNewMessages, 5000); // Se hace la consulta cada 5 segundos

    document.getElementById('messageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
    
        const messageContent = document.getElementById('message').value;
    
        // Validar contenido del mensaje
        if (!messageContent.trim()) return;

        // Evitar que se carguen los mensajes inmediatamente después de enviar
        isSendingMessage = true;
    
        const data = {
            conversation_id: conversationId,
            sender_id: userId,
            content: messageContent
        };
    
        try {
            const response = await fetch('/rsc/sendMessage.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
    
            const result = await response.json();
            if (result.success) {
                console.log('Mensaje enviado');
                document.getElementById('message').value = ''; // Limpiar el input
                
                // Esperar 5 segundos antes de permitir la carga de nuevos mensajes
                setTimeout(() => {
                    isSendingMessage = false;
                    loadNewMessages(); // Cargar mensajes nuevos inmediatamente después de 5 segundos
                }, 1000);
            } else {
                console.error('Error al enviar el mensaje', result.error);
                isSendingMessage = false; // Asegurarse de que se permita cargar mensajes si hubo un error
            }
        } catch (error) {
            console.error('Error en la solicitud', error);
            isSendingMessage = false; // Asegurarse de que se permita cargar mensajes si hubo un error
        }
    });
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}
