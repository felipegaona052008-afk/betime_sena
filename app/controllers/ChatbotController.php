<?php
// app/controllers/ChatbotController.php
// ── Endpoint para el chatbot (Botpress, Dialogflow, etc.) ────────────────────
// NO usa verifyCsrf() porque el bot no maneja cookies/sesiones de usuario.
// Agrega aquí tu lógica de integración cuando tengas el bot listo.

require_once __DIR__ . '/../../core/Controller.php';

class ChatbotController extends Controller {

    public function webhook(): void {
        // Manejar preflight OPTIONS (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(204);
            exit;
        }

        // Leer el body JSON que manda el chatbot
        $body = file_get_contents('php://input');
        $data = json_decode($body, true) ?? [];

        // ── Aquí conectarás Botpress ──────────────────────────────────────────
        // Por ahora solo confirmamos que el webhook funciona.
        $this->json([
            'status'  => 'ok',
            'message' => 'Webhook de BeTimeSENA recibido correctamente.',
            'payload' => $data,   // Eco del payload para pruebas
        ]);
    }
}
