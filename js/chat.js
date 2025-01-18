document.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.getElementById('chatContainer');
    const urlParams = new URLSearchParams(window.location.search);
    const conversationId = urlParams.get('conversation_id');
    const userId = getCookie('user_id'); // Función para obtener el user_id de las cookies

    if (!conversationId || !userId) {
        chatContainer.innerHTML = '<p>Error: No se pudo cargar la conversación.</p>';
        return;
    }

    function loadMessages() {
        $.ajax({
            url: `rsc/getMessages.php?conversation_id=${conversationId}`,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    renderMessages(data.messages);
                } else {
                    console.error('Error loading messages:', data.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX request failed:', { status, error, response: xhr.responseText });
            }
        });
    }

    function renderMessages(messages) {
        chatContainer.innerHTML = ''; // Limpia el contenedor antes de renderizar

        messages.forEach(message => {
            const messageDiv = document.createElement('div');
            messageDiv.className = message.sender_id === parseInt(userId) 
                ? 'message my-message' 
                : 'message their-message';

            messageDiv.innerHTML = `
                <img class="profile-image-chat" src="${message.sender_photo}">
                <p>${message.content}</p>
            `;

            chatContainer.appendChild(messageDiv);
        });

        // Scroll automático al final del chat
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    loadMessages();

    document.getElementById('messageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
    
        const messageContent = document.getElementById('message').value;
    
        // Validar contenido del mensaje
        if (!messageContent.trim()) return;
    
        // Obtener conversation_id y sender_id
        const urlParams = new URLSearchParams(window.location.search);
        const conversationId = urlParams.get('conversation_id');
        const senderId = getCookie('user_id'); // Asumiendo que tienes la cookie user_id
    
        const data = {
            conversation_id: conversationId,
            sender_id: senderId,
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
            } else {
                console.error('Error al enviar el mensaje', result.error);
            }
        } catch (error) {
            console.error('Error en la solicitud', error);
        }
    });
    
    
    // Función para agregar el mensaje al chat (opcional)
    function addMessageToChat(content, isMine) {
        const chatContainer = document.getElementById('chatContainer'); // Asegúrate de tener un contenedor para los mensajes
    
        const messageDiv = document.createElement('div');
        messageDiv.className = isMine ? 'message my-message' : 'message their-message';
        messageDiv.innerHTML = `
            <p>${content}</p>
        `;
    
        chatContainer.appendChild(messageDiv);
    
        // Desplazar hacia abajo automáticamente
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

