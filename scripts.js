document.addEventListener('DOMContentLoaded', function () {
    var textarea = document.getElementById('wp_config_content');
    if (textarea) {
        textarea.scrollTop = textarea.scrollHeight;
    }

    function toggleOtherInput() {
        var select = document.getElementById('wp_memory_limit');
        var otherInput = document.getElementById('wp_memory_limit_other');
        if (select.value === 'other') {
            otherInput.style.display = 'inline';
        } else {
            otherInput.style.display = 'none';
        }
    }
    toggleOtherInput(); // appel initial pour gérer les valeurs par défaut

    document.querySelectorAll('.dashicons-info').forEach(function(el) {
        el.addEventListener('mouseenter', function() {
            var infoBubble = document.createElement('div');
            infoBubble.className = 'info-bubble';
            infoBubble.innerText = el.getAttribute('data-info');
            el.appendChild(infoBubble);
        });
        el.addEventListener('mouseleave', function() {
            var infoBubble = el.querySelector('.info-bubble');
            if (infoBubble) {
                infoBubble.remove();
            }
        });
    });
});