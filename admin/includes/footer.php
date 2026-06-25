<footer class="footer">
    <p>
        <?= CopyrightRicardFS(); ?>
    </p>
</footer>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<!-- Inicializador universal -->
<script src="/admin/assets/js/quill-init.js"></script>

<script>
    // Script para abrir/cerrar el menú responsive
    document.getElementById('menuToggle').addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('open');
    });

    // Editor de texto enrriquecido de Quill
    document.addEventListener("DOMContentLoaded", () => {

        // Inicializar todos los editores Quill de la página
        document.querySelectorAll('.quill-editor').forEach(editorDiv => {

            const target = editorDiv.dataset.target;
            const textarea = document.getElementById(target);

            // Inicializar Quill
            const quill = new Quill(editorDiv, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'header': [1, 2, 3, false]
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        ['link'],
                        [{
                            'align': []
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        ['clean']
                    ]
                }
            });

            // Cargar contenido inicial desde el textarea
            quill.root.innerHTML = textarea.value;

            // Sincronizar cambios del editor al textarea
            quill.on('text-change', function() {
                textarea.value = quill.root.innerHTML;
            });

        });

        // Añadir tooltips a los botones
        const tooltips = {
            bold: 'Negrita',
            italic: 'Cursiva',
            underline: 'Subrayado',
            strike: 'Tachado',
            link: 'Insertar enlace',
            list: 'Lista',
            'list-ordered': 'Lista ordenada',
            'list-bullet': 'Lista con viñetas',
            clean: 'Limpiar formato',
            color: 'Color del texto',
            background: 'Color de fondo',
            align: 'Alineación',
            header: 'Encabezado'
        };

        document.querySelectorAll('.ql-toolbar button, .ql-toolbar span').forEach(btn => {
            const classes = btn.className.split(' ').filter(c => c.startsWith('ql-'));
            if (!classes.length) return;

            let key = classes[0].replace('ql-', '');

            // Si tiene valor (ej: lista ordenada)
            if (btn.dataset && btn.dataset.value) {
                key = `${key}-${btn.dataset.value}`;
            }

            if (tooltips[key]) {
                btn.setAttribute('title', tooltips[key]);
            } else if (tooltips[key.replace(/-.+$/, '')]) {
                btn.setAttribute('title', tooltips[key.replace(/-.+$/, '')]);
            }
        });

    });
</script>

</body>

</html>