<?php
require 'config/config.php';
requireLogin();

$pageTitle = "Help";
include 'includes/header.php';
?>

<div class="help-page">
    <div class="help-header">
        <h1>How can we help you?</h1>
        <p>Find answers to common questions about Post It</p>
    </div>

    <div class="help-content">
        <!-- Getting Started -->
        <div class="help-section">
            <h2>📌 Getting Started</h2>

            <div class="help-item">
                <h3>Create a Note</h3>
                <p>Click the <strong>+</strong> button in the bottom right corner to create a new note. Add a title and description, then click Save.</p>
            </div>

            <div class="help-item">
                <h3>Edit a Note</h3>
                <p>Click on any note card to open and edit it. Make your changes and click Save to update.</p>
            </div>

            <div class="help-item">
                <h3>Pin Important Notes</h3>
                <p>Click the 📍 pin icon on a note to pin it to the top. Click again to unpin.</p>
            </div>
        </div>

        <!-- Organizing Notes -->
        <div class="help-section">
            <h2>📦 Organizing Notes</h2>

            <div class="help-item">
                <h3>Archive Notes</h3>
                <p>Click the 📦 archive icon to move notes you don't need immediately to the Archive. Access them anytime from the Archive page.</p>
            </div>

            <div class="help-item">
                <h3>Change Note Color</h3>
                <p>When editing a note, click any color circle at the bottom to change the note's background color.</p>
            </div>

            <div class="help-item">
                <h3>Delete Notes</h3>
                <p>Click the 🗑️ delete icon in the modal to permanently delete a note. This action cannot be undone.</p>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="help-section">
            <h2>⚙️ Account Settings</h2>

            <div class="help-item">
                <h3>Update Profile</h3>
                <p>Click your profile picture in the header, then select Profile. You can update your username, email, and profile picture.</p>
            </div>

            <div class="help-item">
                <h3>Change Password</h3>
                <p>Go to Profile page and use the Change Password section. You'll need to enter your current password.</p>
            </div>

            <div class="help-item">
                <h3>Dark Mode</h3>
                <p>Click your profile picture and select Dark Mode to switch to a darker theme that's easier on the eyes.</p>
            </div>

            <div class="help-item">
                <h3>Logout</h3>
                <p>Click your profile picture and select Logout to securely end your session.</p>
            </div>
        </div>

        <!-- Keyboard Shortcuts -->
        <div class="help-section">
            <h2>⌨️ Tips</h2>

            <div class="help-item">
                <h3>Quick Actions</h3>
                <ul>
                    <li>Click outside a note card to close it</li>
                    <li>Use the search feature to find notes quickly</li>
                    <li>Pin your most important notes to keep them at the top</li>
                    <li>Archive old notes to keep your main view clean</li>
                </ul>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>