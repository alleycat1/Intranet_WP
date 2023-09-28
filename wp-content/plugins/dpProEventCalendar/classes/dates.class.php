<?php

// Dates Handler Class

class DPPEC_Dates {
	
	var $currentMonth;
	var $currentYear;
	var $currentDate;
	var $prevMonth;
	var $prevYear;
	var $lastDayNum;
	var $firstDayNum;
	var $daysInCurrentMonth;
	var $currentDayNumber;
	var $daysInPrevMonth;
	
	function __construct( $defaultDate ) 
	{

		$this->initialize($defaultDate);
    }
	
	function daysInMonth($year, $month)
    { 
	
        return date("t", strtotime($year . "-" . $month . "-01")); 
		
    }

    function initialize($defaultDate)
    { 

        $this->currentMonth = date('n', $defaultDate);
		$this->currentYear = date('Y', $defaultDate);
		$this->currentDate = date('j', $defaultDate);
		$this->currentDayNumber = date('N', $defaultDate);
		$this->prevMonth = $this->currentMonth - 1 == 0 ? 12 : ($this->currentMonth - 1);
		$this->prevYear = $this->prevMonth == 12 ? ($this->currentYear - 1) : $this->currentYear;
		$this->daysInCurrentMonth = $this->daysInMonth( $this->currentYear, $this->currentMonth );
		$this->daysInPrevMonth = $this->daysInMonth( $this->prevYear, $this->prevMonth );
		$this->lastDayNum = date('w', mktime( 0, 0, 0, $this->currentMonth, $this->daysInCurrentMonth, $this->currentYear ));
		$this->firstDayNum = date('w', mktime( 0, 0, 0, $this->currentMonth, 0, $this->currentYear ));
		
    }
	
}
?>