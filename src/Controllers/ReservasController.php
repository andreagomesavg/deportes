<?php
Class ReservasController
{
    public function __construct(private ReservasGateway $gateway){}
    public function processsRequestReservas(string $method, ?string $id): void
    {
        if($id!=null){
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }
    private function processResourceRequest(string $method, ?string $id): void {
        $reserva = $this -> gateway -> getReserva($id);
        if(!$reserva){
            http_response_code(404);
        echo json_encode(["message" => "reserva con id {$id} no encontrado"]);
        return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($reserva);
                break;
                case "PATCH":
                    $dataReserva = (array) json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($dataReserva, false);
                    if(!empty($errors)){
                        http_response_code(422);
                        echo json_encode(["errors"=>$errors]);
                        break;
                    }
                    $rows =$this->gateway->updateReserva($reserva, $dataReserva);
                    http_response_code(206);
                    echo json_encode(["message" => "Reserva con id: $id ha sido actualizado", "rows affected: "=>$rows]);
                    break;


                case "DELETE": 
                    $rows = $this->gateway->deleteReserva($id);
                    echo json_encode([
                        "message" => "Reserva con id {$id} eliminada",
                        "rows" => "Han sido eliminadas {$rows} filas"
                    ]);
                    break;
                default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
                break;
            }


    }
    private function processCollectionRequest(string $method): void {
        switch($method){
            case "GET":
                echo json_encode($this->gateway->getAllReservas());
                break;


                case "POST":
                 $dataReserva = (array) json_decode(file_get_contents("php://input",true));
                 $errors=$this->getValidationErrors($dataReserva);
                $id=$this->gateway->createReserva($dataReserva);


                if(!empty($errors)){
                    http_response_code(422); //unprocesable entity
                    echo json_encode($errors);
                    break;
                }
                 

                 
                 if($id==0)
                 {
                    http_response_code(409); 
                    echo json_encode([
                        "Reserva no creada debido a que el socio está penalizado" 
                       ]);
                       break;
                 }

                 http_response_code(201);
                 echo json_encode([
                     "message" => "reserva creada", "id" => $id
                    ]);
                    break;
                   
                    default:
                        http_response_code(405);
                        header("Allow: GET, POST");
                        break;
        }
    }


    private function getValidationErrors(array $dataReserva, bool $is_new=true):array{
        $errors=[];
        if(array_key_exists("socio",$dataReserva)){
           if(filter_var($dataReserva["socio"], FILTER_VALIDATE_INT) ===false){
            $errors[] = "El campo socio debe ser de tipo entero (id)";
           }
        }
        if(array_key_exists("pista",$dataReserva)){
            if(filter_var($dataReserva["pista"], FILTER_VALIDATE_INT) ===false){
             $errors[] = "El campo pista debe ser de tipo entero (id)";
            }
         }

         if($is_new && (!isset($dataReserva["fecha"]) || empty($dataReserva["fecha"])) ){
            $errors[] = "la fecha es obligatoria y debe ser un string";
        }
         if(array_key_exists("hora",$dataReserva)){
            if(filter_var($dataReserva["hora"], FILTER_VALIDATE_INT) ===false){
             $errors[] = "El campo hora debe ser de tipo entero (id)";
            }
         }
         if(isset($dataReserva["iluminado"]) && !is_bool($dataReserva["iluminado"])){
            $errors[]="El campo iluminado debe se ser true o false";
        }
        else if(array_key_exists("iluminado", $dataReserva)){
            if (!is_bool($dataReserva["iluminado"])){
                $errors[] = "La reserva debe indicar si se requiere estar iluminado (true) o no (false)";
            }
        }
        
        return $errors;
    }
}