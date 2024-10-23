<?php
class PistasGateway

{

private PDO $con;

public function __construct(Database $database) {

$this->con = $database->getConnection();

}

public function getAllPistas() : Array {

    $sql = "SELECT * FROM pista";
    
    $stmt = $this->con->query($sql);
    
    $dataPistas=[];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    $row["disponible"] = (bool) $row["disponible"];
    
    $dataPistas[] = $row;
    
    }
    
    return $dataPistas;
    
    }

    public function createPista(array $dataPistas) : string {

        $sql = "INSERT INTO pista (nombre, tipo, max_jugadores, disponible) VALUES (:nombre, :tipo, :max_jugadores, :disponible)";
        
        $stmt = $this->con->prepare($sql);
        
        $stmt->bindValue(":nombre", $dataPistas["nombre"] , PDO::PARAM_STR);
        $stmt->bindValue(":tipo", $dataPistas["tipo"] , PDO::PARAM_STR);
        $stmt->bindValue(":max_jugadores", $dataPistas["max_jugadores"] ?? 2 , PDO::PARAM_INT);
        $stmt->bindValue(":disponible", (bool)$dataPistas["disponible"] ?? false , PDO::PARAM_BOOL);
        $stmt->execute();
        return $this->con->lastInsertId();
        
        }

        public function getPista(string $id): array| false {
            $sql = "SELECT * FROM pista WHERE id = :id";
                $stmt = $this -> con -> prepare($sql);
                $stmt -> bindValue(":id", $id, PDO:: PARAM_INT);
                $stmt -> execute();
                $data = $stmt -> fetch (PDO::FETCH_ASSOC);
        
                if ($data !== false) {
                    $data["disponible"] = (bool) $data["disponible"];
                }
                return $data;
        }

        public function updatePista(array $current, array $new): int 
    {
        $sql = "UPDATE pista SET nombre= :nombre, tipo= :tipo, max_jugadores= :max_jugadores, disponible= :disponible WHERE id= :id";
        $stmt = $this->con->prepare($sql);
        $stmt -> bindValue(":nombre", $new["nombre"]?? $current["nombre"], PDO::PARAM_STR);
        $stmt -> bindValue(":tipo", $new["tipo"]?? $current["tipo"], PDO::PARAM_STR);
        $stmt -> bindValue(":max_jugadores", $new["max_jugadores"]?? $current["max_jugadores"], PDO::PARAM_INT);
        $stmt -> bindValue("disponible", $new["disponible"]?? $current["disponible"], PDO::PARAM_BOOL);

        $stmt -> bindValue(":id",$current["id"],PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> rowCount();
    }
    public function deletePista(string $id): int{
        $sql = "DELETE FROM pista 
        WHERE id = :id
        AND NOT EXISTS (
            SELECT 1 
            FROM reserva 
            WHERE reserva.pista = pista.id
        );";
        $stmt = $this -> con->prepare($sql);
        $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
    
        $stmt -> execute();
        return $stmt -> rowCount();
    }
}