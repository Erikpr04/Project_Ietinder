function sendLog(action) {
    console.log("Action to send:", action);
    fetch('log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ action: action }), 
    })
    .catch(error => {
        console.error("Error in fetch:", error);
    });
}


