// Test script per verificare la registrazione
// Questo script simula il comportamento del frontend

const testRegistration = async () => {
    console.log('🧪 Inizio test registrazione frontend...');
    
    try {
        // Configurazione axios come nel frontend
        axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
        axios.defaults.headers.common['Accept'] = 'application/json';
        axios.defaults.headers.common['Content-Type'] = 'application/json';
        
        // Dati di test
        const userData = {
            nome: 'TestFrontend',
            cognome: 'UserTest',
            email: 'frontend@test.com',
            password: 'password123',
            password_confirmation: 'password123',
            titolo_studi: 'Diploma',
            data_nascita: '1990-01-01',
            citta_nascita: 'Milano'
        };
        
        console.log('📤 Invio dati:', userData);
        
        // Chiamata di registrazione
        const response = await axios.post('/auth/register', userData);
        
        console.log('✅ Registrazione riuscita:', response.data);
        console.log('📄 Status:', response.status);
        console.log('📋 Headers:', response.headers);
        
    } catch (error) {
        console.error('❌ Errore registrazione:', error);
        console.error('📄 Status:', error.response?.status);
        console.error('📋 Data:', error.response?.data);
        console.error('📋 Headers:', error.response?.headers);
    }
};

// Esegui il test quando la pagina è caricata
if (window.axios) {
    testRegistration();
} else {
    console.error('Axios non è disponibile');
}