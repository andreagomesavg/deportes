<?php
Class PistasController {
    public function __construct(private PistasGateway $gateway){}
    public function processRequestPistas(string $method, ?string $id){
        if($id){
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }
    private function processResourceRequest(string $method, string $id) : void {
        $pista = $this -> gateway -> getPista($id);
        if(!$pista){
            http_response_code(404);
        echo json_encode(["message" => "Pista con id {$id} no encontrada"]);
        return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($pista);
                break;
                case "PATCH":
                    $dataPista = (array) json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($dataPista, false);
                    if(!empty($errors)){
                        http_response_code(422);
                        echo json_encode(["errors"=>$errors]);
                        break;
                    }
                    $rows =$this->gateway->updatePista($pista, $dataPista);
                    http_response_code(206);
                    echo json_encode(["message" => "Pista con id: $id ha sido actualizado", "rows affected: "=>$rows]);
                    break;
                    case "DELETE": 
                        $rows = $this->gateway->deletePista($id);
                        if($rows==0){
                            http_response_code(409);
                            echo json_encode("No puedes eliminar una pista con reservas. Elimina primero sus reservas.");
                        }else{
                        echo json_encode([
                            "message" => "Pista con id {$id} eliminado",
                            "rows" => "Han sido eliminadas {$rows} filas"
                        ]);}
                        break;
               
                default:
                http_response_code(405);
                header("Permitido: GET, PATCH, DELETE");
                break;
            }
    }
    
    private function processCollectionRequest(string $method) : void {
        switch($method) {

            case "GET":
                echo json_encode( $this->gateway->getAllPistas() );
            break;
            case "POST":
                $dataPistas = (array) json_decode(file_get_contents("php://input", true) );
                $errors=$this->getValidationErrors($dataPistas);
                if(!empty($errors)){
                    http_response_code(422); 
                    echo json_encode($errors);
                    break;
                }
                $id = $this->gateway->createPista($dataPistas);
                http_response_code(201);
                echo json_encode([ "message" => "Pista creada", "id" => $id ]);
            break;
            
            default:
            
            http_response_code(405);
            // method not allowed
            
            header("METODOS PERMITIDOS: GET, POST");
            // informar disponibles
            
            break;
    }
}
    private function getValidationErrors(array $dataPistas, bool $is_new=true):array{
        $errors=[];

        if($is_new && (!isset($dataPistas["nombre"]) || empty($dataPistas["nombre"])) ){
            $errors[] = "El nombre es obligatorio";
        }
        if($is_new && (!isset($dataPistas["tipo"]) || empty($dataPistas["tipo"])) ){
            $errors[] = "El tipo es obligatorio";
        }
        if(array_key_exists("max_jugadores",$dataPistas)){
           if(filter_var($dataPistas["max_jugadores"], FILTER_VALIDATE_INT) ===false){
            $errors[] = "El campo de max_jugadores debe ser de tipo entero";
           }
        }
        if(isset($dataPistas["disponible"]) && !is_bool($dataPistas["disponible"])){
            $errors[]="La pista disponible debe se ser true o false";
        }
        else if(array_key_exists("disponible", $dataPistas)){
            if (!is_bool($dataPistas["disponible"])){
                $errors[] = "La pista debe estar disponible (true) o no (false)";
            }
        }

        return $errors;
    }
}