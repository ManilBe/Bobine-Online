// popup.js

document.addEventListener("DOMContentLoaded", function() {
    const watchButton = document.getElementById("watchButton");
    const closePopupButton = document.getElementById("closePopup");
    const popupContainer = document.getElementById("popupContainer");

    // Ouvrir la popup
    if (watchButton) {
        watchButton.addEventListener("click", function() {
            popupContainer.style.display = "flex"; // Affiche la popup
        });
    }

    // Fermer la popup
    if (closePopupButton) {
        closePopupButton.addEventListener("click", function() {
            popupContainer.style.display = "none"; // Cache la popup
        });
    }
});
