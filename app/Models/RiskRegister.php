<?php
namespace App\Models;

class RiskRegister
{
    private $id;
    private $facultyId;
    private $objective;
    private $riskEvent;
    private $likelihoodInherent;
    private $impactInherent;
    private $riskLevelInherent;

    // constructor opsional
    public function __construct($facultyId = null, $objective = null, $riskEvent = null)
    {
        $this->facultyId = $facultyId;
        $this->objective = $objective;
        $this->riskEvent = $riskEvent;
    }

    // Getter & Setter
    public function getId() { return $this->id; }
    public function setId($val) { $this->id = $val; }

    public function getFacultyId() { return $this->facultyId; }
    public function setFacultyId($val) { $this->facultyId = $val; }

    public function getObjective() { return $this->objective; }
    public function setObjective($val) { $this->objective = $val; }

    public function getRiskEvent() { return $this->riskEvent; }
    public function setRiskEvent($val) { $this->riskEvent = $val; }

    public function getLikelihoodInherent() { return $this->likelihoodInherent; }
    public function setLikelihoodInherent($val) { $this->likelihoodInherent = $val; }

    public function getImpactInherent() { return $this->impactInherent; }
    public function setImpactInherent($val) { $this->impactInherent = $val; }

    public function getRiskLevelInherent() { return $this->riskLevelInherent; }
    public function setRiskLevelInherent($val) { $this->riskLevelInherent = $val; }
}
