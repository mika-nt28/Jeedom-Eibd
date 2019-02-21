<?php
class EIS14_ABB_ControlAcces {
 public function RD_WHITE_LIST(){
  /*
  Byte 1 = Command code 0x42
  Bytes 2,3 = Index of the item in the list from which to start reading.
  Byte 4 = number of blocks to read (1 block = 6 items on the list).
  Bytes 5,6,7,8,9,10,11,12,13 and 14 = 00
  */
 }
 public function RD_BLACK_LIST(){
  /*
  Byte 1 = Command code 0x43
  Byte 2,3 = Reading start item
  Byte 4 = Number of blocks to read (1 block = 6 items on the list).
  Bytes 5,6,7,8,9,10,11,12,13 and 14 = 00
  */
 }
 public function RD_TIME_TAB(){
  /*
  Byte 1 = Command code 0x47
  Byte 2 = Number of reading start timeslot 01 / FF)
  Byte 3 = Number of blocks to read (1 block = 2 timeslots).
  Bytes 4,5,6,7,8,9,10,11,12.13 and 14 = 00
  */
 }
 public function RD_ACC_DATA14(){
  /*
  Byte 1 = Command code 0x49
  Byte 2 = Number of accesses to read
  Bytes 3,4,5,6,7,8,9,10,11,12,13 and 14 = 00
  */
 }
 public function RD_PLT_CODE(){
  /*
  Byte 1 = Command code 0x45
  Bytes 2 = Index of the item in the plant codes list from which to start reading.
  Bytes 3 = number of blocks to read (1 block = 4 items in the list).
  Bytes 4,5,6,7,8,9,10,11,12.13 and 14 = 00
  */
 }
 public function RD_GRP_ASS_TBL(){
  /*
  Byte 1 = Command code 0x4F
  Bytes 2 = Group index
  Bytes 3 = Reading start index.
  MAXIMUM NUMBER OF TIMESLOTS ATTRIBUTABLE TO A SINGLE GROUP => 256 X 13 = 3328! 
  */
 }
 public function WR_INS_WL(){
  /*Byte 1 = Command code 0x88
  Byte 2,3 = Code 1
  Byte 4,5 = Code 2
  Byte 6,7 = Code 3
  Byte 8,9 = Code 4
  Byte 10,11 = Code 5
  Byte 12,13 = Code 6
  Byte 14 = 00
  N.B. The codes with a value of 00 00 will not be added (NULL code)
  */
 }
 public function WR_DEL_WL(){
  /*Byte 1 = Command code 0x89
  Byte 2,3 = Code 1
  Byte 4,5 = Code 2
  Byte 6,7 = Code 3
  Byte 8,9 = Code 4
  Byte 10,11 = Code 5
  Byte 12,13 = Code 6
  Byte 14 = 00
  N.B. codes with a value of 00 00 will not be eliminated (NULL code)
  */
 }
 public function WR_INS_BL(){
  /*Byte 1 = Command code 0x8B
  Byte 2,3 = Code 1
  Byte 4,5 = Code 2
  Byte 6,7 = Code 3
  Byte 8,9 = Code 4
  Byte 10,11 = Code 5
  Byte 12,13 = Code 6
  Byte 14 = 00
  N.B. The codes with a value of 00 00 will not be added (NULL code) 
  */
 }
 public function WR_DEL_BL(){
  /*Byte 1 = Command code 0x8C
  Byte 2,3 = Code 1
  Byte 4,5 = Code 2
  Byte 6,7 = Code 3
  Byte 8,9 = Code 4
  Byte 10,11 = Code 5
  Byte 12,13 = Code 6
  Byte 14 = 0
  N.B. codes with a value of 00 00 will not be eliminated (NULL code)
  */
 }
 public function WR_PLT_CODE(){
  /*Byte 1 = Command code 0x8E
  Byte 2,3,4 = Plant Code1
  Byte 5,6,7 = Plant Code 2
  Byte 8,9,10 = Plant Code 3
  Byte 11,12,13 = Plant Code 4
  Byte 14 = 00
  Code PC 00 is the null
  */
 }
 public function WR_DEL_PLT_CODE(){
  /*Byte 1 = command code 0x97
  Byte 2,3,4 = code 1
  Byte 5,6,7 = code 2
  Byte 8,9,10 = code 3
  Byte 11,12,13 = code 4
  Byte 14 = 00
  */
 }
 public function WRITE_TIME(){
  /*Byte 1 = Command code 0x8F
  Byte 2 = Day
  Byte 3 = Month
  Byte 4 = Year
  Byte 5 = Hour
  Byte 6 = Minutes
  Byte 7 = Seconds
  Byte 8 = Day of the week 1=Mon;2=Tue;3=Wed;4=Thu;5=Fri;6=Sat;7=Sun; 0=undef.
  Byte 9,10,11,12,13,14 = 00 
  */
 }
 public function WR_TIME_TAB(){
  /*Byte 1 = Command code 0x92
  Byte 2 = First timeslot index (timeslot 00 is the NULL slot, and must not be considered!)
  Byte 3 = bit (0,1,2) Day of the week (0=all - 1=Mon 2=Tue etc.)
  Byte 3 = bit (3,4,5,6,7) Slot start time, hour.
  Byte 4 = bit (0,1,2,3,4,5) Slot start time, minutes.
  Byte 4 = bit (6,7) = (00= Negative Slot - 11=Positive Slot)
  Byte 5 = bit (0,1,2) Day of the week (0=all - 1=Mon 2=Tue etc.)
  Byte 5 = bit (3,4,5,6,7) Slot end time, hour.
  Byte 6 = bit (0,1,2,3,4,5) Slot end time, minutes.
  Byte 6 = bit (6,7) = (00= Negative Slot - 11=Positive Slot)
  Byte 2 = Second timeslot index (timeslot 00 is the NULL slot, and must not be considered!)
  Byte 8 = bit (0,1,2) Day of the week (0=all - 1=Mon 2=Tue etc.)
  Byte 8 = bit (3,4,5,6,7) Slot start time, hour.
  Byte 9 = bit (0,1,2,3,4,5) Slot start time, minutes.
  Byte 9 = bit (6,7) = (00= Negative Slot - 11=Positive Slot)
  Byte 10= bit (0,1,2) Day of the week (0=all - 1=Mon 2=Tue etc.)
  Byte 10= bit(3,4,5,6,7) Slot end time, hour.
  Byte 11= bit (0,1,2,3,4,5) Slot end time, minutes.
  Byte 11= bit (6,7) = (00= Negative Slot - 11=Positive Slot)
  Byte 12 = Number of associated group (00 01 – FF FF, group no. 0000 is the NULL)
  Byte 13 = Number of associated group (00 01 – FF FF, group no. 0000 is the NULL)
  Byte 14 = Number of associated group (00 01 – FF FF, group no. 0000 is the NULL)
  */
 }
 public function WR_INS_GRP_ASS_TBL(){
  /*Byte 1 = command code 0x98
  Byte 2 Group (0001- FFFF) Note: Type 00 00 does not exist, numbering must start from group 1.
  Byte 3 = Slot (00-FF)
  Byte 4 = Slot (00-FF)
  Byte 5 = Slot (00-FF)
  Byte 6 = Slot (00-FF)
  .....
  Byte 14 = Timeslot (00-FF)
  */
 }
 public function WR_BLK1(){
  /*Byte 1 = command code 0XA1
  Byte 2 = TAG byte 0
  Byte 3 = TAG byte 1
  Byte 4 = TAG byte 2
  Byte 5 = TAG byte 3
  Byte 6 = TAG byte 5
  Byte 6 = TAG byte 6
  Byte 6 = 00
  .....
  Byte 14 = 00
  */
 }
 public function WR_BLK2(){
  /*Byte 1 = command code 0XA2
  Byte 2 = TAG byte 7
  Byte 3 = TAG byte 8
  Byte 4 = TAG byte 9
  Byte 5 = TAG byte 10
  Byte 6 = TAG byte 11
  Byte 7 = TAG byte 12
  Byte 8 = TAG byte 13
  .....
  Byte 14 = 00
  */
 }
 public function WR_DEL_GRP_ASS_TBL(){
  /*Byte 1 = command code 0XA5
  Byte 2 Group (01- FF) Note: Type 00 00 does not exist, numbering must start from group 1.
  Byte 3 = Slot (00-FF) to delete
  Byte 4 = Slot (00-FF) to delete
  Byte 5 = Slot (00-FF) to delete
  Byte 6 = Slot (00-FF) to delete
  .....
  Byte 14= Slot (00-FF)
  */
 }
 public function WR_DEL_TIME_TBL(){
  /*Byte 1 = command code 0XA8
  Byte 2 = Slot (00-FF) to delete
  Byte 3 = Slot (00-FF) to delete
  Byte 4 = Slot (00-FF) to delete
  Byte 5 = Slot (00-FF) to delete
  Byte 6 = Slot (00-FF) to delete .....
  Byte 14= Slot (00-FF)
  */
 }
 public function WR_PRICE_TBL(){
  /*Byte 1 = Command Code 0x9E
  Byte 2 = rate profile 1, byte 1
  Byte 3 = rate profile 1, byte 2
  Byte 4 = rate profile 1, byte 3
  Byte 5 = rate profile 2, byte 1
  Byte 6 = rate profile 2, byte 2
  Byte 7 = rate profile 2, byte 3
  Byte 8 = rate profile 3, byte 1
  Byte 9 = rate profile 3, byte 2
  Byte 10= rate profile 3, byte 3
  Byte 11= rate profile 4, byte 1
  Byte 12= rate profile 4, byte 2
  Byte 13= rate profile 4, byte 3
  Byte 14= 0x00 */
 }
}
?>
