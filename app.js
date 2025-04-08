document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const registerBtn = e.target.querySelector('button[type="submit"]');
    registerBtn.disabled = true;
    registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';

    try {
        const response = await fetch('http://localhost:3000/api/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('register-name').value,
                email: document.getElementById('register-email').value,
                password: document.getElementById('register-password').value
            })
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.error || 'Error desconocido');

        alert(`¡Registro exitoso! ID: ${data.userId}`);
        document.getElementById('login-tab').click();
        e.target.reset();

    } catch (error) {
        alert(`Error: ${error.message}`);
    } finally {
        registerBtn.disabled = false;
        registerBtn.innerHTML = '<i class="fas fa-user-plus"></i> Crear cuenta';
    }
});