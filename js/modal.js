// modal.js

function initModal(modalID, openBtnID, closeBtnID, cancelBtnID) {
    const modal = document.getElementById(modalID);
    const openBtn = document.getElementById(openBtnID);
    const closeBtn = document.getElementById(closeBtnID);
    const cancelBtn = document.getElementById(cancelBtnID);

    if (openBtn) {
        openBtn.onclick = () => modal.classList.add("show");
    }
    if (closeBtn) {
        closeBtn.onclick = () => modal.classList.remove("show");
    }
    if (cancelBtn) {
        cancelBtn.onclick = () => modal.classList.remove("show");
    }

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
}

function initEditModal(modalID, closeBtnID, cancelBtnID, fieldsSelector) {
    const modal = document.getElementById(modalID);
    const closeBtn = document.getElementById(closeBtnID);
    const cancelBtn = document.getElementById(cancelBtnID);

    closeBtn.onclick = cancelBtn.onclick = () => modal.classList.remove("show");

    document.querySelectorAll(".editBtn").forEach((btn) => {
        btn.onclick = () => {
            for (const field in fieldsSelector) {
                const el = document.getElementById(fieldsSelector[field]);
                el.value = btn.dataset[field];
            }
            modal.classList.add("show");
        };
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
}
