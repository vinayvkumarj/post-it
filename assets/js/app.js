// Load dark mode preference immediately
(function () {
  const savedDarkMode = localStorage.getItem("darkMode") === "true";
  if (savedDarkMode) {
    document.body.classList.add("dark-mode");
  }
})();

document.addEventListener("DOMContentLoaded", function () {
  const currentPage = window.location.pathname.includes("archive.php")
    ? "archive"
    : "notes";
  const addNoteBtn = document.getElementById("addNoteBtn");
  const noteModal = document.getElementById("noteModal");
  const closeModal = document.getElementById("closeModal");
  const cancelBtn = document.getElementById("cancelBtn");
  const saveBtn = document.getElementById("saveBtn");
  const noteId = document.getElementById("noteId");
  const noteTitle = document.getElementById("noteTitle");
  const noteDescription = document.getElementById("noteDescription");
  const pinBtn = document.getElementById("pinBtn");
  const archiveBtn = document.getElementById("archiveBtn");
  const deleteBtn = document.getElementById("deleteBtn");
  const toast = document.getElementById("toast");
  const colorCircles = document.querySelectorAll(".color-circle");
  const noteDates = document.getElementById("noteDates");
  const createdDateEl = document.getElementById("createdDate");
  const updatedDateEl = document.getElementById("updatedDate");
  const settingsBtn = document.getElementById("settingsBtn");
  const dropdownMenu = document.querySelector(".dropdown-menu");

  let currentNoteId = null;
  let isSaving = false;

  // ============================================
  // SETTINGS DROPDOWN 
  // ============================================

  if (settingsBtn && dropdownMenu) {
    settingsBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      dropdownMenu.classList.toggle("active");
    });

    document.addEventListener("click", function (e) {
      if (!settingsBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove("active");
      }
    });

    dropdownMenu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        dropdownMenu.classList.remove("active");
      });
    });
  }

  // ============================================
  // DARK MODE TOGGLE 
  // ============================================

  const darkModeToggle = document.getElementById("darkModeToggle");

  if (darkModeToggle) {
    const savedDarkMode = localStorage.getItem("darkMode") === "true";
    const textSpan = darkModeToggle.querySelector("span");
    if (textSpan) {
      textSpan.textContent = savedDarkMode ? "Light Mode" : "Dark Mode";
    }

    darkModeToggle.addEventListener("click", function (e) {
      e.preventDefault();
      document.body.classList.toggle("dark-mode");

      const isDark = document.body.classList.contains("dark-mode");
      localStorage.setItem("darkMode", isDark);

      const textSpan = this.querySelector("span");
      if (textSpan) {
        textSpan.textContent = isDark ? "Light Mode" : "Dark Mode";
      }
    });
  }

  // ============================================
  // PROFILE PICTURE UPLOAD
  // ============================================

  const profilePic = document.getElementById("profilePic");
  const profilePicUpload = document.getElementById("profilePicUpload");

  if (profilePic && profilePicUpload) {
    profilePic.addEventListener("click", function () {
      profilePicUpload.click();
    });
  }

  // Auto-hide alerts on profile page
  const alerts = document.querySelectorAll(".alert");
  if (alerts.length > 0) {
    setTimeout(() => {
      alerts.forEach((alert) => {
        alert.style.opacity = "0";
        alert.style.transition = "opacity 0.3s";
        setTimeout(() => (alert.style.display = "none"), 300);
      });
    }, 3000);
  }

  // Reload page after profile pic upload to update header
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("uploaded") === "1") {
    setTimeout(() => {
      window.location.reload();
    }, 500);
  }

  // ============================================
  // NOTES FUNCTIONALITY - ONLY ON NOTES/ARCHIVE PAGES
  // ============================================

  if (!noteModal) {
    console.log("❌ No modal found, skipping notes functionality");
    return;
  }

  console.log("✅ Modal found, initializing notes functionality");

  if (addNoteBtn) {
    addNoteBtn.addEventListener("click", function () {
      openModal();
    });
  } else {
    console.log("ℹ️ No addNoteBtn found (this is OK for archive page)");
  }

  closeModal.addEventListener("click", closeModalFunc);
  cancelBtn.addEventListener("click", closeModalFunc);

  noteModal.addEventListener("click", function (e) {
    if (e.target === noteModal) {
      closeModalFunc();
    }
  });

  // Use event delegation for note cards
  document.body.addEventListener("click", function (e) {
    const card = e.target.closest(".note-card");
    if (card && !e.target.closest(".note-icon")) {
      console.log("📝 Opening note:", card.dataset.noteId);

      const id = card.dataset.noteId;
      const title = card.dataset.noteTitle || "";
      const description = card.dataset.noteDescription || "";
      const color = card.dataset.noteColor || "#ffffff";
      const pinned = card.dataset.notePinned || 0;
      const created = card.dataset.noteCreated || "";
      const updated = card.dataset.noteUpdated || "";

      openModal(id, title, description, color, pinned, created, updated);
    }

    const icon = e.target.closest(".note-icon");
    if (icon) {
      e.stopPropagation();
      const action = icon.dataset.action;
      const id = icon.dataset.noteId;

      console.log("🎯 Icon clicked:", action, "on note:", id);

      if (action === "toggle-pin") {
        togglePin(id);
      } else if (action === "toggle-archive") {
        toggleArchive(id);
      } else if (action === "delete") {
        if (
          !confirm(
            "Are you sure you want to delete this note? This cannot be undone.",
          )
        ) {
          return;
        }
        fetch("delete_note.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ note_id: id }),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result.success) {
              showToast("Note deleted");
              setTimeout(() => location.reload(), 500);
            } else {
              showToast(result.error || "Failed to delete note");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showToast("Failed to delete note");
          });
      }
    }
  });

  // Handle color circle clicks
  colorCircles.forEach(function (circle) {
    circle.addEventListener("click", function () {
      colorCircles.forEach(function (c) {
        c.classList.remove("active");
      });

      this.classList.add("active");

      const isDark = document.body.classList.contains("dark-mode");
      const color = this.dataset.color;

      if (isDark) {
        const darkColors = {
          "#ffffff": "#3c4043",
          "#fff": "#3c4043",
          "#FFF8E1": "#5c4a2a",
          "#FFF475": "#7a6b1f",
          "#CCFF90": "#4a6b2a",
          "#AECBFA": "#2a4a6b",
          "#FDCFE8": "#6b2a4a",
        };

        const darkColor = darkColors[color] || "#3c4043";

        document.querySelector(".modal-content").style.backgroundColor =
          darkColor;
        noteTitle.style.backgroundColor = darkColor;
        noteDescription.style.backgroundColor = darkColor;
      } else {
        document.querySelector(".modal-content").style.backgroundColor = color;
        noteTitle.style.backgroundColor = color;
        noteDescription.style.backgroundColor = color;
      }
    });
  });

  function openModal(
    id = null,
    title = "",
    description = "",
    color = "#ffffff",
    pinned = 0,
    created = "",
    updated = "",
  ) {
    currentNoteId = id;
    noteId.value = id || "";
    noteTitle.value = title || "";
    noteDescription.value = description || "";

    colorCircles.forEach(function (circle) {
      if (circle.dataset.color === color) {
        circle.classList.add("active");
      } else {
        circle.classList.remove("active");
      }
    });

    const isDark = document.body.classList.contains("dark-mode");

    if (isDark) {
      const darkColors = {
        "#ffffff": "#3c4043",
        "#fff": "#3c4043",
        "#FFF8E1": "#5c4a2a",
        "#FFF475": "#7a6b1f",
        "#CCFF90": "#4a6b2a",
        "#AECBFA": "#2a4a6b",
        "#FDCFE8": "#6b2a4a",
      };

      const darkColor = darkColors[color] || "#3c4043";

      document.querySelector(".modal-content").style.backgroundColor =
        darkColor;
      noteTitle.style.backgroundColor = darkColor;
      noteDescription.style.backgroundColor = darkColor;
    } else {
      document.querySelector(".modal-content").style.backgroundColor = color;
      noteTitle.style.backgroundColor = color;
      noteDescription.style.backgroundColor = color;
    }

    if (id && created && updated) {
      noteDates.style.display = "block";
      createdDateEl.textContent = "Created " + created;
      updatedDateEl.textContent = "Updated " + updated;
    } else {
      noteDates.style.display = "none";
    }

    // ✅ Update pin button icon and tooltip (using Lucide SVG)
    if (pinned == 1) {
      pinBtn.innerHTML = '<i data-lucide="pin-off" class="modal-icon"></i>';
      pinBtn.title = "Unpin";
    } else {
      pinBtn.innerHTML = '<i data-lucide="pin" class="modal-icon"></i>';
      pinBtn.title = "Pin";
    }

    // ✅ Update archive button icon and tooltip (using Lucide SVG)
    if (currentPage === "archive") {
      archiveBtn.innerHTML =
        '<i data-lucide="archive-restore" class="modal-icon"></i>';
      archiveBtn.title = "Unarchive";
    } else {
      archiveBtn.innerHTML =
        '<i data-lucide="archive" class="modal-icon"></i>';
      archiveBtn.title = "Archive";
    }

    // ✅ CRITICAL: Re-render Lucide icons after changing innerHTML
    if (typeof lucide !== "undefined") {
      lucide.createIcons();
    }

    noteModal.classList.add("active");
  }

  function closeModalFunc() {
    noteModal.classList.remove("active");
    currentNoteId = null;
  }

  // Save Note
  saveBtn.addEventListener("click", function () {
    if (isSaving) return;
    isSaving = true;

    const data = {
      note_id: noteId.value,
      title: noteTitle.value.trim(),
      description: noteDescription.value.trim(),
      color:
        document.querySelector(".color-circle.active")?.dataset.color ||
        "#ffffff",
    };

    if (!data.title && !data.description) {
      showToast("Note must have a title or description");
      isSaving = false;
      return;
    }

    fetch("save_note.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          showToast("Note saved!");
          closeModalFunc();
          setTimeout(() => location.reload(), 500);
        } else {
          showToast(result.error || "Failed to save note");
          isSaving = false;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showToast("Failed to save note");
        isSaving = false;
      });
  });

  // Pin from Modal
  pinBtn.addEventListener("click", function () {
    if (!currentNoteId) return;
    togglePin(currentNoteId);
  });

  // Archive/Unarchive from Modal
  archiveBtn.addEventListener("click", function () {
    if (!currentNoteId) return;
    toggleArchive(currentNoteId);
  });

  // Delete from Modal
  deleteBtn.addEventListener("click", function () {
    if (!currentNoteId) return;

    if (
      !confirm(
        "Are you sure you want to delete this note? This cannot be undone.",
      )
    ) {
      return;
    }

    fetch("delete_note.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ note_id: currentNoteId }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          showToast("Note deleted");
          closeModalFunc();
          setTimeout(() => location.reload(), 500);
        } else {
          showToast(result.error || "Failed to delete note");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showToast("Failed to delete note");
      });
  });

  // Helper Functions
  function togglePin(id) {
    fetch("toggle_pin.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ note_id: id }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          showToast("Pin toggled");
          setTimeout(() => location.reload(), 500);
        } else {
          showToast(result.error || "Failed to toggle pin");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showToast("Failed to toggle pin");
      });
  }

  function toggleArchive(id) {
    fetch("toggle_archive.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ note_id: id }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          const isArchivePage = currentPage === "archive";
          showToast(isArchivePage ? "Note unarchived" : "Note archived");
          setTimeout(() => location.reload(), 500);
        } else {
          showToast(result.error || "Failed to archive note");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showToast("Failed to archive note");
      });
  }

  function showToast(message) {
    toast.textContent = message;
    toast.classList.add("show");
    setTimeout(() => {
      toast.classList.remove("show");
    }, 3000);
  }

  // ✅ Initialize Lucide icons on page load
  if (typeof lucide !== "undefined") {
    lucide.createIcons();
    console.log("✅ Lucide icons initialized");
  }
});