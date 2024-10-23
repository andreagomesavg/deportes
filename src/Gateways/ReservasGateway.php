<?php
class ReservasGateway{
    private PDO $con;
    public function __construct(Database $database){
        $this->con = $database->getConnection();
    }


    public function getAllReservas():Array {
        $sql = "SELECT * FROM reserva";
        $stmt = $this->con->query($sql);
        $dataReserva=[];
        while($row= $stmt->fetch(PDO::FETCH_ASSOC)){
            $row["iluminar"]=(bool)$row["iluminar"];
            $dataReserva[]= $row;
    }
    return $dataReserva;
}
public function createreserva(array $dataReserva):string {
    /*$sql = "INSERT INTO reserva(socio, pista, fecha, hora, iluminar)
        SELECT :socio, :pista, :fecha, :hora, :iluminar
        FROM socio
        WHERE socio.id = :socio AND socio.penalizado = false";
         $stmt = $this->con->prepare($sql);
         $stmt->bindValue(":socio", $dataReserva["socio"], PDO::PARAM_INT);
         $stmt->bindValue(":pista", $dataReserva["pista"], PDO::PARAM_INT);
         $stmt->bindValue(":fecha", $dataReserva["fecha"], PDO::PARAM_STR);
         $stmt->bindValue(":hora", $dataReserva["hora"], PDO::PARAM_INT);
         $stmt->bindValue(":iluminar", (bool)$dataReserva["iluminar"] ?? false, PDO::PARAM_BOOL);
         $stmt->execute();
        return $this->con->lastInsertId();*/
        $sql = "INSERT INTO reserva(socio, pista, fecha, hora, iluminar)
        SELECT :socio, :pista, :fecha, :hora, :iluminar
        WHERE EXISTS (
            SELECT 1 FROM socio
            WHERE id = :id AND penalizado = false
        )
    ";
    
    $stmt = $this->con->prepare($sql);
    $stmt->bindValue(":socio", $dataReserva["socio"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $dataReserva["socio"], PDO::PARAM_INT); // Para el WHERE EXISTS
    $stmt->bindValue(":pista", $dataReserva["pista"], PDO::PARAM_INT);
    $stmt->bindValue(":fecha", $dataReserva["fecha"] ?? 0, PDO::PARAM_STR);
    $stmt->bindValue(":hora", $dataReserva["hora"] ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(":iluminar", (bool)$dataReserva["iluminar"] ?? false, PDO::PARAM_BOOL);

    $stmt->execute();
    
    return $this->con->lastInsertId();
}


public function getreserva(string $id): array| false {
    $sql = "SELECT * FROM reserva WHERE id = :id";
        $stmt = $this -> con -> prepare($sql);
        $stmt -> bindValue(":id", $id, PDO:: PARAM_INT);
        $stmt -> execute();
        $dataReserva = $stmt -> fetch (PDO::FETCH_ASSOC);


        if ($dataReserva !== false) {
            $dataReserva["iluminar"] = (bool) $dataReserva["iluminar"];
        }
        return $dataReserva;
}


public function updatereserva(array $current, array $new): int
    {
        $sql = "UPDATE reserva SET socio= :socio, pista= :pista, fecha= :fecha, hora= :hora, iluminar= :iluminar WHERE id= :id";
        $stmt = $this->con->prepare($sql);
        $stmt -> bindValue(":socio", $new["socio"]?? $current["socio"], PDO::PARAM_INT);
        $stmt -> bindValue(":pista", $new["pista"]?? $current["pista"], PDO::PARAM_INT);  
        $stmt -> bindValue(":fecha", $new["fecha"]?? $current["fecha"], PDO::PARAM_STR);
        $stmt -> bindValue(":hora", $new["hora"]?? $current["hora"], PDO::PARAM_INT);
        $stmt -> bindValue(":iluminar", $new["iluminar"]?? $current["iluminar"], PDO::PARAM_BOOL);


        $stmt -> bindValue(":id",$current["id"],PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> rowCount();
    }




public function deletereserva(string $id): int{
    $sql = "DELETE FROM reserva WHERE id= :id";
    $stmt = $this -> con->prepare($sql);
    $stmt -> bindValue(":id", $id, PDO::PARAM_INT);


    $stmt -> execute();
    return $stmt -> rowCount();
}
}
