<?php
class EIS14_ABB_ControlAcces {
 public function RD_WHITE_LIST(){
  /*
  Byte 1 = Command code 0x42
  Bytes 2,3 = Index of the item in the list from which to start reading.
  Byte 4 = number of blocks to read (1 block = 6 items on the list).
  Bytes 5,6,7,8,9,10,11,12,13 and 14 = 00
  */
  $Bytes[0]=0x42;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function RD_BLACK_LIST(){
  /*
  Byte 1 = Command code 0x43
  Byte 2,3 = Reading start item
  Byte 4 = Number of blocks to read (1 block = 6 items on the list).
  Bytes 5,6,7,8,9,10,11,12,13 and 14 = 00
  */
  $Bytes[0]=0x43;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function RD_TIME_TAB(){
  /*
  Byte 1 = Command code 0x47
  Byte 2 = Number of reading start timeslot 01 / FF)
  Byte 3 = Number of blocks to read (1 block = 2 timeslots).
  Bytes 4,5,6,7,8,9,10,11,12.13 and 14 = 00
  */
  $Bytes[0]=0x47;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]=0;
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function RD_ACC_DATA14(){
  /*
  Byte 1 = Command code 0x49
  Byte 2 = Number of accesses to read
  Bytes 3,4,5,6,7,8,9,10,11,12,13 and 14 = 00
  */
  $Bytes[0]=0x49;
  $Bytes[1]='';
  $Bytes[2]=0;
  $Bytes[3]=0;
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function RD_PLT_CODE(){
  /*
  Byte 1 = Command code 0x45
  Bytes 2 = Index of the item in the plant codes list from which to start reading.
  Bytes 3 = number of blocks to read (1 block = 4 items in the list).
  Bytes 4,5,6,7,8,9,10,11,12.13 and 14 = 00
  */
  $Bytes[0]=0x45;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]=0;
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function RD_GRP_ASS_TBL(){
  /*
  Byte 1 = Command code 0x4F
  Bytes 2 = Group index
  Bytes 3 = Reading start index.
  MAXIMUM NUMBER OF TIMESLOTS ATTRIBUTABLE TO A SINGLE GROUP => 256 X 13 = 3328! 
  */
  $Bytes[0]=0x4F;
  $Bytes[1]='';
  $Bytes[2]='';
  return $Bytes;
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
  $Bytes[0]=0x88;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]='';
  $Bytes[5]='';
  $Bytes[6]='';
  $Bytes[7]='';
  $Bytes[8]='';
  $Bytes[9]='';
  $Bytes[10]='';
  $Bytes[11]='';
  $Bytes[12]='';
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x89;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]='';
  $Bytes[5]='';
  $Bytes[6]='';
  $Bytes[7]='';
  $Bytes[8]='';
  $Bytes[9]='';
  $Bytes[10]='';
  $Bytes[11]='';
  $Bytes[12]='';
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x8B;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]='';
  $Bytes[5]='';
  $Bytes[6]='';
  $Bytes[7]='';
  $Bytes[8]='';
  $Bytes[9]='';
  $Bytes[10]='';
  $Bytes[11]='';
  $Bytes[12]='';
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x8C;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]='';
  $Bytes[5]='';
  $Bytes[6]='';
  $Bytes[7]='';
  $Bytes[8]='';
  $Bytes[9]='';
  $Bytes[10]='';
  $Bytes[11]='';
  $Bytes[12]='';
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x8E;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]='';
  $Bytes[5]='';
  $Bytes[6]='';
  $Bytes[7]='';
  $Bytes[8]='';
  $Bytes[9]='';
  $Bytes[10]='';
  $Bytes[11]='';
  $Bytes[12]='';
  $Bytes[13]=0;
  return $Bytes;
 }
 public function WR_DEL_PLT_CODE(){
  /*Byte 1 = command code 0x97
  Byte 2,3,4 = code 1
  Byte 5,6,7 = code 2
  Byte 8,9,10 = code 3
  Byte 11,12,13 = code 4
  Byte 14 = 00
  */
  $Bytes[0]=0x97;
  $Bytes[1]='';
  $Bytes[2]='';
  $Bytes[3]='';
  $Bytes[4]='';
  $Bytes[5]='';
  $Bytes[6]='';
  $Bytes[7]='';
  $Bytes[8]='';
  $Bytes[9]='';
  $Bytes[10]='';
  $Bytes[11]='';
  $Bytes[12]='';
  $Bytes[13]=0;
  return $Bytes;
 }
 public function WRITE_TIME(){
  $Bytes[0]=0x8F;
  $Bytes[1]=date("d");
  $Bytes[2]=date("m");
  $Bytes[3]=date("Y");
  $Bytes[4]=date("H");
  $Bytes[5]=date("i");
  $Bytes[6]=date("s");
  $Bytes[7]=date("N");
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function WR_TIME_TAB($index1,$start1,$stop1,$index2,$start2,$stop2){
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
  
  $start1=strtotime($start1);
  $stop1=strtotime($stop1);
  $start2=strtotime($start2);
  $stop2=strtotime($stop2);
  $Bytes[0]=0x92;
  $Bytes[1]=$index1;
  $Bytes[2]=(0xF8 & date("H",$start1)) << 3 | 0x07 & date("N",$start1);
  $Bytes[3]=0x03 << 6 | 0xF & date("i",$start1);
  $Bytes[4]=(0xF8 & date("H",$stop1)) << 3 | 0x07 & date("N",$stop1);
  $Bytes[5]=0x03 << 6 | 0xF & date("i",$stop1);
  $Bytes[6]=$index2;
  $Bytes[7]=(0xF8 & date("H",$start2)) << 3 | 0x07 & date("N",$start2);
  $Bytes[8]=0x03 << 6 | 0xF & date("i",$start2);
  $Bytes[9]=(0xF8 & date("H",$stop2)) << 3 | 0x07 & date("N",$stop2);
  $Bytes[10]=0x03 << 6 | 0xF & date("i",$stop2);
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x42;
  $Bytes[1]=0x42;
  $Bytes[2]=0x42;
  $Bytes[3]=0x42;
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function WR_BLK1($Tag){
  $Bytes
  $Bytes[0]=0xA1;
  $Bytes[1]=substr($Tag,0,2);
  $Bytes[2]=substr($Tag,2,2);
  $Bytes[3]=substr($Tag,4,2);
  $Bytes[4]=substr($Tag,6,2);
  $Bytes[5]=substr($Tag,8,2);
  $Bytes[6]=substr($Tag,10,2);
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
 public function WR_BLK2($Tag){
  $Bytes
  $Bytes[0]=0xA2;
  $Bytes[1]=substr($Tag,0,2);
  $Bytes[2]=substr($Tag,2,2);
  $Bytes[3]=substr($Tag,4,2);
  $Bytes[4]=substr($Tag,6,2);
  $Bytes[5]=substr($Tag,8,2);
  $Bytes[6]=substr($Tag,10,2);
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x42;
  $Bytes[1]=0x42;
  $Bytes[2]=0x42;
  $Bytes[3]=0x42;
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
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
  $Bytes[0]=0x42;
  $Bytes[1]=0x42;
  $Bytes[2]=0x42;
  $Bytes[3]=0x42;
  $Bytes[4]=0;
  $Bytes[5]=0;
  $Bytes[6]=0;
  $Bytes[7]=0;
  $Bytes[8]=0;
  $Bytes[9]=0;
  $Bytes[10]=0;
  $Bytes[11]=0;
  $Bytes[12]=0;
  $Bytes[13]=0;
  return $Bytes;
 }
}
?>
