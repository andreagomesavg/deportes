<?php
class SociosGateway{
    private PDO $con;
    public function __construct(Database $database){
        $this->con = $database->getConnection();
    }

    public function getAllSocio():Array {
        $sql = "SELECT * FROM socio";
        $stmt = $this->con->query($sql);
        $data=[];
        while($row= $stmt->fetch(PDO::FETCH_ASSOC)){
            $row["penalizado"]=(bool)$row["penalizado"];
            $data[]= $row;
    }
    return $data;
}
public function createSocio(array $data):string {
    $sql = "INSERT INTO socio(nombre, telefono, edad, penalizado) VALUES(:nombre, :telefono, :edad, :penalizado)";
        $stmt = $this->con->prepare($sql);
        $stmt ->bindValue(":nombre",$data["nombre"], PDO::PARAM_STR);
        $stmt ->bindValue(":telefono",$data["telefono"]??0, PDO::PARAM_STR);
        $stmt ->bindValue(":edad",$data["edad"]??0, PDO::PARAM_INT);
        $stmt->bindValue(":penalizado",(bool)$data["penalizado"]??false , PDO::PARAM_BOOL);
        $stmt ->execute();
        return $this->con->lastInsertId();
}

public function getSocio(string $id): array| false {
    $sql = "SELECT * FROM socio WHERE id = :id";
        $stmt = $this -> con -> prepare($sql);
        $stmt -> bindValue(":id", $id, PDO:: PARAM_INT);
        $stmt -> execute();
        $data = $stmt -> fetch (PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data["penalizado"] = (bool) $data["penalizado"];
        }
        return $data;
}

public function updateSocio(array $current, array $new): int 
    {
        $sql = "UPDATE socio SET nombre= :nombre, telefono= :telefono, edad= :edad, penalizado= :penalizado WHERE id= :id";
        $stmt = $this->con->prepare($sql);
        $stmt -> bindValue(":nombre", $new["nombre"]?? $current["nombre"], PDO::PARAM_STR);
        $stmt -> bindValue(":telefono", $new["telefono"]?? $current["telefono"], PDO::PARAM_STR);
        $stmt -> bindValue(":edad", $new["edad"]?? $current["edad"], PDO::PARAM_INT);
        $stmt -> bindValue(":penalizado", $new["penalizado"]?? $current["penalizado"], PDO::PARAM_BOOL);

        $stmt -> bindValue(":id",$current["id"],PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> rowCount();
    }


public function deleteSocio(string $id): int{
    $sql = "DELETE FROM socio 
WHERE id = :id
AND NOT EXISTS (
    SELECT 1 
    FROM reserva 
    WHERE reserva.socio = socio.id
);";
    $stmt = $this -> con->prepare($sql);
    $stmt -> bindValue(":id", $id, PDO::PARAM_INT);

    $stmt -> execute();
    return $stmt -> rowCount();
}
}