const modal = document.getElementById('editorModal');
const closeBtn = document.querySelector('.close');
const editorContent = document.getElementById('editorContent');
const editFileName = document.getElementById('editFileName');
const editorForm = document.getElementById('editorForm');

function openEditor(fileName, content) {
    editFileName.value = fileName;
    editorContent.value = content;
    modal.style.display = 'block';
}

closeBtn.onclick = function() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Handle edit links
document.querySelectorAll('.edit-file').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        fetch(this.href)
            .then(response => response.text())
            .then(content => {
                openEditor(this.dataset.file, content);
            });
    });
});