<?php 
require_once '../config/config.php';
require_once '../includes/Database.php';

class Model {
    protected $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function obtenerReservaciones($consecutivo, $email) {
        try {
            $sql = "
                SELECT 
                    p.name as proveedor_name,
                    u.username,
                    re.created_at,
                    re.id, 
                    ho.proveedor,
                    ho.number_booking,                       
                    re.consecutivo, 
                    ho.price_public AS public_price,
                    ho.price_net AS net_price,
                    (ho.price_public - ho.price_net) AS utilidad,
                    ((ho.price_public - ho.price_net) * 0.84) AS utilidad_menos_16,        
                    re.executive_id, 
                    re.id_reference,
                    ho.liquidated,
                    ho.pay_status,
                    ho.status as status_servicio,
                    'Hotel' AS tipo_servicio,
                    rm.total_pending
                FROM `hotel_reservations` ho 
                LEFT JOIN reservations re ON re.id = ho.id_reservations
                LEFT JOIN customers cu ON re.customer_id = cu.id
                LEFT JOIN reservation_amount rm ON rm.id_reservation = re.id 
                LEFT JOIN users u on u.id = re.executive_id
                LEFT JOIN providers p on p.id = ho.proveedor
                WHERE re.consecutivo = :consecutivo
                AND cu.email = :email
                AND re.mode = 'PRD'

                UNION ALL

                SELECT 
                    p.name as proveedor_name,
                    u.username,
                    re.created_at,
                    re.id, 
                    fli.providers,
                    fli.number_confirmation, 
                    re.consecutivo,
                    (
                        SELECT fr_flights.public_price
                        FROM flight_reservation_flights fr_flights 
                        WHERE fr_flights.flight_reservation_id = fli.id 
                        LIMIT 1
                    ) AS public_price,
                    (
                        SELECT fr_flights.net_price
                        FROM flight_reservation_flights fr_flights 
                        WHERE fr_flights.flight_reservation_id = fli.id 
                        LIMIT 1
                    ) AS net_price,
                    (
                        SELECT fr_flights.public_price - fr_flights.net_price
                        FROM flight_reservation_flights fr_flights 
                        WHERE fr_flights.flight_reservation_id = fli.id 
                        LIMIT 1
                    ) AS utilidad,
                    (
                        SELECT fr_flights.public_price - fr_flights.net_price * 0.84
                        FROM flight_reservation_flights fr_flights 
                        WHERE fr_flights.flight_reservation_id = fli.id 
                        LIMIT 1
                    ) AS utilidad_menos_16,
                    re.executive_id,  
                    re.id_reference,
                    fli.liquidated,
                    fli.pay_status,
                    fli.status as status_servicio,
                    'Vuelo' AS tipo_servicio,
                    rm.total_pending
                FROM flight_reservations fli 
                LEFT JOIN reservations re ON re.id = fli.id_reservation
                LEFT JOIN customers cu ON re.customer_id = cu.id
                LEFT JOIN reservation_amount rm ON rm.id_reservation = re.id 
                LEFT JOIN users u on u.id = re.executive_id
                LEFT JOIN providers p on p.id = fli.providers
                WHERE re.consecutivo = :consecutivo
                AND cu.email = :email
                AND re.mode = 'PRD'

                UNION ALL

                SELECT 
                    p.name as proveedor_name,
                    u.username,
                    re.created_at,
                    re.id, 
                    trans.provider_id,
                    trans.confirmation_number AS number_confirmation,                       
                    re.consecutivo, 
                    trans.public_price AS public_price,
                    trans.net_price AS net_price,
                    (trans.public_price - trans.net_price) AS utilidad,
                    ((trans.public_price - trans.net_price) * 0.84) AS utilidad_menos_16,        
                    re.executive_id, 
                    re.id_reference,
                    trans.liquidated,
                    trans.pay_status,      
                    trans.status as status_servicio,                                                  
                    'Transportacion' AS tipo_servicio,
                    rm.total_pending
                FROM `transfer_reservations` trans 
                LEFT JOIN reservations re on re.id = trans.reservation_id
                LEFT JOIN reservation_amount rm on rm.id_reservation = re.id
                LEFT JOIN users u on u.id = re.executive_id
                LEFT JOIN providers p on p.id = trans.provider_id
                LEFT JOIN companies c ON c.id = u.company_id 
                LEFT JOIN customers cu ON re.customer_id = cu.id
                WHERE re.consecutivo = :consecutivo
                AND cu.email = :email
                AND re.mode = 'PRD'

                UNION ALL

                SELECT 
                    p.name as proveedor_name,
                    u.username,
                    re.created_at,
                    re.id, 
                    tour.providers,
                    tour.number_confirmation AS number_confirmation,                       
                    re.consecutivo, 
                    tour.price_public AS public_price,
                    tour.price_net AS net_price,
                    (tour.price_public - tour.price_net) AS utilidad,
                    ((tour.price_public - tour.price_net) * 0.84) AS utilidad_menos_16,        
                    re.executive_id, 
                    re.id_reference,
                    tour.liquidated,
                    tour.pay_status,
                    tour.status as status_servicio,
                    'Tour' AS tipo_servicio,
                    rm.total_pending
                FROM `tour_reservations` tour 
                LEFT JOIN reservations re on re.id = tour.id_reservation
                LEFT JOIN reservation_amount rm on rm.id_reservation = re.id 
                LEFT JOIN users u on u.id = re.executive_id
                LEFT JOIN providers p on p.id = tour.providers
                LEFT JOIN companies c ON c.id = u.company_id
                LEFT JOIN customers cu ON re.customer_id = cu.id
                WHERE re.consecutivo = :consecutivo
                AND cu.email = :email
                AND re.mode = 'PRD'

                UNION ALL
    
                SELECT 
                    p.name as proveedor_name,
                    u.username,
                    re.created_at,
                    re.id, 
                    mini.providers,
                    'N/A' AS number_confirmation,                       
                    re.consecutivo, 
                    mini.public_price AS public_price,
                    mini.net_price AS net_price,
                    (mini.public_price - mini.net_price) AS utilidad,
                    ((mini.public_price - mini.net_price) * 0.84) AS utilidad_menos_16,        
                    re.executive_id, 
                    re.id_reference,
                    mini.liquidated,
                    mini.pay_status,
                    mini.status as status_servicio,
                    'Minivac' AS tipo_servicio,
                    rm.total_pending
                FROM `minivac_reservations` mini 
                LEFT JOIN reservations re on re.id = mini.id_reservations
                LEFT JOIN reservation_amount rm on rm.id_reservation = re.id 
                LEFT JOIN users u on u.id = re.executive_id
                LEFT JOIN providers p on p.id = mini.providers
                LEFT JOIN companies c ON c.id = u.company_id
                LEFT JOIN customers cu ON re.customer_id = cu.id
                WHERE re.consecutivo = :consecutivo
                AND cu.email = :email
                AND re.mode = 'PRD'

                UNION ALL

                SELECT 
                    p.name as proveedor_name,
                    u.username,
                    re.created_at,
                    re.id, 
                    cir.providers,
                    'N/A' AS number_confirmation,                       
                    re.consecutivo, 
                    cir.public_price AS public_price,
                    cir.net_price AS net_price,
                    (cir.public_price - cir.net_price) AS utilidad,
                    ((cir.public_price - cir.net_price) * 0.84) AS utilidad_menos_16,        
                    re.executive_id, 
                    re.id_reference,
                    cir.liquidated,
                    cir.pay_status,
                    cir.status as status_servicio,
                    'Circuitos' AS tipo_servicio,
                    rm.total_pending
                FROM `circuits_reservations` cir 
                LEFT JOIN reservations re on re.id = cir.id_reservations
                LEFT JOIN reservation_amount rm on rm.id_reservation = re.id 
                LEFT JOIN users u on u.id = re.executive_id
                LEFT JOIN providers p on p.id = cir.providers
                LEFT JOIN companies c ON c.id = u.company_id
                LEFT JOIN customers cu ON re.customer_id = cu.id
                WHERE re.consecutivo = :consecutivo
                AND cu.email = :email
                AND re.mode = 'PRD'
                ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":consecutivo", $consecutivo);
            $stmt->bindParam(":email", $email);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Error en la consulta: " . $e->getMessage()];
        }
    }


}
?>