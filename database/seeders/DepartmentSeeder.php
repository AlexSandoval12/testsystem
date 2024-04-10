<?php

namespace Database\Seeders;

use App\Models\Ciudad;
use App\Models\Departamento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departamentos = [[
                            "id"=> 1,
                            "departamento"=> "CONCEPCION"
                          ],
                          [
                            "id"=> 2,
                            "departamento"=> "SAN PEDRO"
                          ],
                          [
                            "id"=> 3,
                            "departamento"=> "CORDILLERA"
                          ],
                          [
                            "id"=> 4,
                            "departamento"=> "GUAIRA"
                          ],
                          [
                            "id"=> 5,
                            "departamento"=> "CAAGUAZU"
                          ],
                          [
                            "id"=> 6,
                            "departamento"=> "CAAZAPA"
                          ],
                          [
                            "id"=> 7,
                            "departamento"=> "ITAPUA"
                          ],
                          [
                            "id"=> 8,
                            "departamento"=> "MISIONES"
                          ],
                          [
                            "id"=> 9,
                            "departamento"=> "PARAGUARI"
                          ],
                          [
                            "id"=> 10,
                            "departamento"=> "ALTO PARANA"
                          ],
                          [
                            "id"=> 11,
                            "departamento"=> "CENTRAL"
                          ],
                          [
                            "id"=> 12,
                            "departamento"=> "ÑEEMBUCU"
                          ],
                          [
                            "id"=> 13,
                            "departamento"=> "AMAMBAY"
                          ],
                          [
                            "id"=> 14,
                            "departamento"=> "CANINDEYU"
                          ],
                          [
                            "id"=> 15,
                            "departamento"=> "PRESIDENTE HAYES"
                          ],
                          [
                            "id"=> 16,
                            "departamento"=> "BOQUERON"
                          ],
                          [
                            "id"=> 17,
                            "departamento"=> "ALTO PARAGUAY"
                          ],
                          [
                            "id"=> 18,
                            "departamento"=> "ASUNCION DC"
                          ]];

        $ciudad = [[
                    "id"=> 1,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Asunción",
                  ],
                  [
                    "id"=> 2,
                    "departamentos_id"=> 17,//ALTO PARAGUAY
                    "ciudad"=> "Bahía Negra",
                  ],
                  [
                    "id"=> 3,
                    "departamentos_id"=> 17,//ALTO PARAGUAY
                    "ciudad"=> "Carmelo Peralta",
                  ],
                  [
                    "id"=> 4,
                    "departamentos_id"=> 17,//ALTO PARAGUAY
                    "ciudad"=> "Puerto Casado",
                  ],
                  [
                    "id"=> 5,
                    "departamentos_id"=> 17,//ALTO PARAGUAY
                    "ciudad"=> "Fuerte Olimpo",
                  ],
                  [
                    "id"=> 6,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Ciudad del Este",
                  ],
                  [
                    "id"=> 7,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Doctor Juan León Mallorquín",
                  ],
                  [
                    "id"=> 8,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Doctor Raúl Peña",
                  ],
                  [
                    "id"=> 9,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Domingo Martínez de Irala",
                  ],
                  [
                    "id"=> 10,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Hernandarias",
                  ],
                  [
                    "id"=> 11,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Iruña",
                  ],
                  [
                    "id"=> 12,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Itakyry",
                  ],
                  [
                    "id"=> 13,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Juan E. O´Leary",
                  ],
                  [
                    "id"=> 14,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Los Cedrales",
                  ],
                  [
                    "id"=> 15,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Mbaracayú",
                  ],
                  [
                    "id"=> 16,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Minga Guazú",
                  ],
                  [
                    "id"=> 17,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Minga Porá",
                  ],
                  [
                    "id"=> 18,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Naranjal",
                  ],
                  [
                    "id"=> 19,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Ñacunday",
                  ],
                  [
                    "id"=> 20,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Presidente Franco",
                  ],
                  [
                    "id"=> 21,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "San Alberto",
                  ],
                  [
                    "id"=> 22,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "San Cristóbal",
                  ],
                  [
                    "id"=> 23,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Santa Fe del Paraná",
                  ],
                  [
                    "id"=> 24,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Santa Rita",
                  ],
                  [
                    "id"=> 25,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Santa Rosa del Monday",
                  ],
                  [
                    "id"=> 26,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Tavapy",
                  ],
                  [
                    "id"=> 27,
                    "departamentos_id"=> 10,//ALTO PARANA
                    "ciudad"=> "Colonia Yguazú",
                  ],
                  [
                    "id"=> 28,
                    "departamentos_id"=> 13,//AMAMBAY
                    "ciudad"=> "Bella Vista Norte",
                  ],
                  [
                    "id"=> 29,
                    "departamentos_id"=> 13,//AMAMBAY
                    "ciudad"=> "Capitán Bado",
                  ],
                  [
                    "id"=> 30,
                    "departamentos_id"=> 13,//AMAMBAY
                    "ciudad"=> "Pedro Juan Caballero",
                  ],
                  [
                    "id"=> 31,
                    "departamentos_id"=> 13,//AMAMBAY
                    "ciudad"=> "Zanja Pytá",
                  ],
                  [
                    "id"=> 32,
                    "departamentos_id"=> 13,//AMAMBAY
                    "ciudad"=> "Karapaí",
                  ],
                  [
                    "id"=> 33,
                    "departamentos_id"=> 16,//BOQUERON
                    "ciudad"=> "Filadelfia",
                  ],
                  [
                    "id"=> 34,
                    "departamentos_id"=> 16,//BOQUERON
                    "ciudad"=> "Loma Plata",
                  ],
                  [
                    "id"=> 35,
                    "departamentos_id"=> 16,//BOQUERON
                    "ciudad"=> "Mcal. Estigarribia",
                  ],
                  [
                    "id"=> 36,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Caaguazú",
                  ],
                  [
                    "id"=> 37,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Carayaó",
                  ],
                  [
                    "id"=> 38,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Cnel. Oviedo",
                  ],
                  [
                    "id"=> 39,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Doctor Cecilio Báez",
                  ],
                  [
                    "id"=> 40,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Doctor Juan Eulogio Estigarribia - Campo 9",
                  ],
                  [
                    "id"=> 41,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Campo 9",
                  ],
                  [
                    "id"=> 42,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Doctor Juan Manuel Frutos",
                  ],
                  [
                    "id"=> 43,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "José Domingo Ocampos",
                  ],
                  [
                    "id"=> 44,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "La Pastora",
                  ],
                  [
                    "id"=> 45,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Mcal. Francisco S. López",
                  ],
                  [
                    "id"=> 46,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Nueva Londres",
                  ],
                  [
                    "id"=> 47,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Nueva Toledo",
                  ],
                  [
                    "id"=> 48,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Raúl Arsenio Oviedo",
                  ],
                  [
                    "id"=> 49,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Repatriación",
                  ],
                  [
                    "id"=> 50,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "R. I. Tres Corrales",
                  ],
                  [
                    "id"=> 51,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "San Joaquín",
                  ],
                  [
                    "id"=> 52,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "San José de los Arroyos",
                  ],
                  [
                    "id"=> 53,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Mbutuy",
                  ],
                  [
                    "id"=> 54,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Simón Bolívar",
                  ],
                  [
                    "id"=> 55,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Tembiaporá",
                  ],
                  [
                    "id"=> 56,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Tres de Febrero",
                  ],
                  [
                    "id"=> 57,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Vaquería",
                  ],
                  [
                    "id"=> 58,
                    "departamentos_id"=> 5,//CAAGUAZU
                    "ciudad"=> "Yhú",
                  ],
                  [
                    "id"=> 59,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "3 de Mayo",
                  ],
                  [
                    "id"=> 60,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Abaí",
                  ],
                  [
                    "id"=> 61,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Buena Vista",
                  ],
                  [
                    "id"=> 62,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Caazapá",
                  ],
                  [
                    "id"=> 63,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Doctor Moisés S. Bertoni",
                  ],
                  [
                    "id"=> 64,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Fulgencio Yegros",
                  ],
                  [
                    "id"=> 65,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "General Higinio Morínigo",
                  ],
                  [
                    "id"=> 66,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Maciel",
                  ],
                  [
                    "id"=> 67,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "San Juan Nepomuceno",
                  ],
                  [
                    "id"=> 68,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Tavaí",
                  ],
                  [
                    "id"=> 69,
                    "departamentos_id"=> 6,//CAAZAPA
                    "ciudad"=> "Yuty",
                  ],
                  [
                    "id"=> 71,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Corpus Christi",
                  ],
                  [
                    "id"=> 72,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Curuguaty",
                  ],
                  [
                    "id"=> 73,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Gral. Francisco Caballero Álvarez",
                  ],
                  [
                    "id"=> 74,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Itanará",
                  ],
                  [
                    "id"=> 75,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Katueté",
                  ],
                  [
                    "id"=> 76,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "La Paloma",
                  ],
                  [
                    "id"=> 77,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Maracaná",
                  ],
                  [
                    "id"=> 78,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Nueva Esperanza",
                  ],
                  [
                    "id"=> 79,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Salto del Guairá",
                  ],
                  [
                    "id"=> 80,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Villa Ygatimí",
                  ],
                  [
                    "id"=> 81,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Yasy Cañy",
                  ],
                  [
                    "id"=> 82,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Ybyrarovaná",
                  ],
                  [
                    "id"=> 83,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Ypejhú",
                  ],
                  [
                    "id"=> 84,
                    "departamentos_id"=> 14,//CANINDEYU
                    "ciudad"=> "Yby Pytá",
                  ],
                  [
                    "id"=> 85,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Areguá",
                  ],
                  [
                    "id"=> 86,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Capiatá",
                  ],
                  [
                    "id"=> 87,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Fernando de la Mora",
                  ],
                  [
                    "id"=> 88,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Guarambaré",
                  ],
                  [
                    "id"=> 89,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Itá",
                  ],
                  [
                    "id"=> 90,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Itauguá",
                  ],
                  [
                    "id"=> 91,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "J. Augusto Saldivar",
                  ],
                  [
                    "id"=> 92,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Lambaré",
                  ],
                  [
                    "id"=> 93,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Limpio",
                  ],
                  [
                    "id"=> 94,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Luque",
                  ],
                  [
                    "id"=> 95,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Mariano Roque Alonso",
                  ],
                  [
                    "id"=> 96,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Ñemby",
                  ],
                  [
                    "id"=> 97,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Nueva Italia",
                  ],
                  [
                    "id"=> 98,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "San Antonio",
                  ],
                  [
                    "id"=> 99,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "San Lorenzo",
                  ],
                  [
                    "id"=> 100,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Villa Elisa",
                  ],
                  [
                    "id"=> 101,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Villeta",
                  ],
                  [
                    "id"=> 102,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Ypacaraí",
                  ],
                  [
                    "id"=> 103,
                    "departamentos_id"=> 11,//CENTRAL
                    "ciudad"=> "Ypané",
                  ],
                  [
                    "id"=> 104,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Arroyito",
                  ],
                  [
                    "id"=> 105,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Azotey",
                  ],
                  [
                    "id"=> 106,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Belén",
                  ],
                  [
                    "id"=> 107,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Concepción",
                  ],
                  [
                    "id"=> 108,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Horqueta",
                  ],
                  [
                    "id"=> 109,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Loreto",
                  ],
                  [
                    "id"=> 110,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "San Carlos del Apa",
                  ],
                  [
                    "id"=> 111,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "San Lázaro",
                  ],
                  [
                    "id"=> 112,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Yby Yaú",
                  ],
                  [
                    "id"=> 113,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Sargento José Félix López",
                  ],
                  [
                    "id"=> 114,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "San Alfredo",
                  ],
                  [
                    "id"=> 115,
                    "departamentos_id"=> 1,//CONCEPCION
                    "ciudad"=> "Paso Barreto",
                  ],
                  [
                    "id"=> 116,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Altos",
                  ],
                  [
                    "id"=> 117,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Arroyos y Esteros",
                  ],
                  [
                    "id"=> 118,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Atyrá",
                  ],
                  [
                    "id"=> 119,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Caacupé",
                  ],
                  [
                    "id"=> 120,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Caraguatay",
                  ],
                  [
                    "id"=> 121,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Emboscada",
                  ],
                  [
                    "id"=> 122,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Eusebio Ayala",
                  ],
                  [
                    "id"=> 123,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Isla Pucú",
                  ],
                  [
                    "id"=> 124,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Itacurubí de la Cordillera",
                  ],
                  [
                    "id"=> 125,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Juan de Mena",
                  ],
                  [
                    "id"=> 126,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Loma Grande",
                  ],
                  [
                    "id"=> 127,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Mbocayaty del Yhaguy",
                  ],
                  [
                    "id"=> 128,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Nueva Colombia",
                  ],
                  [
                    "id"=> 129,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Piribebuy",
                  ],
                  [
                    "id"=> 130,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Primero de Marzo",
                  ],
                  [
                    "id"=> 131,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "San Bernardino",
                  ],
                  [
                    "id"=> 132,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "San José Obrero",
                  ],
                  [
                    "id"=> 133,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Santa Elena",
                  ],
                  [
                    "id"=> 134,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Tobatí",
                  ],
                  [
                    "id"=> 135,
                    "departamentos_id"=> 3,//COORDILLERA
                    "ciudad"=> "Valenzuela",
                  ],
                  [
                    "id"=> 136,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Borja",
                  ],
                  [
                    "id"=> 137,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Colonia Independencia",
                  ],
                  [
                    "id"=> 138,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Coronel Martínez",
                  ],
                  [
                    "id"=> 139,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Dr. Bottrell",
                  ],
                  [
                    "id"=> 140,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Fassardi",
                  ],
                  [
                    "id"=> 141,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Félix Pérez Cardozo",
                  ],
                  [
                    "id"=> 142,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Garay",
                  ],
                  [
                    "id"=> 143,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Itapé",
                  ],
                  [
                    "id"=> 144,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Iturbe",
                  ],
                  [
                    "id"=> 145,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Mbocayaty",
                  ],
                  [
                    "id"=> 146,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Natalicio Talavera",
                  ],
                  [
                    "id"=> 147,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Ñumí",
                  ],
                  [
                    "id"=> 148,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Paso Yobái",
                  ],
                  [
                    "id"=> 149,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "San Salvador",
                  ],
                  [
                    "id"=> 150,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Tebicuary",
                  ],
                  [
                    "id"=> 151,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Troche",
                  ],
                  [
                    "id"=> 152,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Villarrica",
                  ],
                  [
                    "id"=> 153,
                    "departamentos_id"=> 4,//GUAIRA
                    "ciudad"=> "Yataity",
                  ],
                  [
                    "id"=> 154,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Alto Verá",
                  ],
                  [
                    "id"=> 155,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Bella Vista",
                  ],
                  [
                    "id"=> 156,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Cambyretá",
                  ],
                  [
                    "id"=> 157,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Capitán Meza",
                  ],
                  [
                    "id"=> 158,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Capitán Miranda",
                  ],
                  [
                    "id"=> 159,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Carlos Antonio López",
                  ],
                  [
                    "id"=> 160,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Carmen del Paraná",
                  ],
                  [
                    "id"=> 161,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Coronel Bogado",
                  ],
                  [
                    "id"=> 162,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Edelira",
                  ],
                  [
                    "id"=> 163,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Encarnación",
                  ],
                  [
                    "id"=> 164,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Fram",
                  ],
                  [
                    "id"=> 165,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "General Artigas",
                  ],
                  [
                    "id"=> 166,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "General Delgado",
                  ],
                  [
                    "id"=> 167,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Hohenau",
                  ],
                  [
                    "id"=> 168,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Itapúa Poty",
                  ],
                  [
                    "id"=> 169,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Jesús",
                  ],
                  [
                    "id"=> 170,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Colonia La Paz",
                  ],
                  [
                    "id"=> 171,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "José Leandro Oviedo",
                  ],
                  [
                    "id"=> 172,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Mayor Otaño",
                  ],
                  [
                    "id"=> 173,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Natalio",
                  ],
                  [
                    "id"=> 174,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Nueva Alborada",
                  ],
                  [
                    "id"=> 175,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Obligado",
                  ],
                  [
                    "id"=> 176,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Pirapó",
                  ],
                  [
                    "id"=> 177,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "San Cosme y Damián",
                  ],
                  [
                    "id"=> 178,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "San Juan del Paraná",
                  ],
                  [
                    "id"=> 179,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "San Pedro del Paraná",
                  ],
                  [
                    "id"=> 180,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "San Rafael del Paraná",
                  ],
                  [
                    "id"=> 181,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Tomás Romero Pereira (Maria Auxiliadora)",
                  ],
                  [
                    "id"=> 182,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Trinidad",
                  ],
                  [
                    "id"=> 183,
                    "departamentos_id"=> 7,//ITAPUA
                    "ciudad"=> "Yatytay",
                  ],
                  [
                    "id"=> 184,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "Ayolas",
                  ],
                  [
                    "id"=> 185,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "San Ignacio",
                  ],
                  [
                    "id"=> 186,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "San Juan Bautista",
                  ],
                  [
                    "id"=> 187,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "San Miguel",
                  ],
                  [
                    "id"=> 188,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "San Patricio",
                  ],
                  [
                    "id"=> 189,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "Santa María",
                  ],
                  [
                    "id"=> 190,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "Santa Rosa de Lima",
                  ],
                  [
                    "id"=> 191,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "Santiago",
                  ],
                  [
                    "id"=> 192,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "Villa Florida",
                  ],
                  [
                    "id"=> 193,
                    "departamentos_id"=> 8,//MISIONES
                    "ciudad"=> "Yabebyry",
                  ],
                  [
                    "id"=> 194,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Alberdi",
                  ],
                  [
                    "id"=> 195,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Cerrito",
                  ],
                  [
                    "id"=> 196,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Desmochados",
                  ],
                  [
                    "id"=> 197,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "General José Eduvigis Díaz",
                  ],
                  [
                    "id"=> 198,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Guazú Cuá",
                  ],
                  [
                    "id"=> 199,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Humaitá",
                  ],
                  [
                    "id"=> 200,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Isla Umbú",
                  ],
                  [
                    "id"=> 201,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Laureles",
                  ],
                  [
                    "id"=> 202,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Mayor José J. Martínez",
                  ],
                  [
                    "id"=> 203,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Paso de Patria",
                  ],
                  [
                    "id"=> 204,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Pilar",
                  ],
                  [
                    "id"=> 205,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "San Juan Bautista del Ñeembucú",
                  ],
                  [
                    "id"=> 206,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Tacuaras",
                  ],
                  [
                    "id"=> 207,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Villa Franca",
                  ],
                  [
                    "id"=> 208,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Villalbín",
                  ],
                  [
                    "id"=> 209,
                    "departamentos_id"=> 12,//ÑEEMBUCU
                    "ciudad"=> "Villa Oliva",
                  ],
                  [
                    "id"=> 210,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Acahay",
                  ],
                  [
                    "id"=> 211,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Caapucú",
                  ],
                  [
                    "id"=> 212,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Carapeguá",
                  ],
                  [
                    "id"=> 213,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Escobar",
                  ],
                  [
                    "id"=> 214,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Gral. Bernardino Caballero",
                  ],
                  [
                    "id"=> 215,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "La Colmena",
                  ],
                  [
                    "id"=> 216,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "María Antonia",
                  ],
                  [
                    "id"=> 217,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Mbuyapey",
                  ],
                  [
                    "id"=> 218,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Paraguarí",
                  ],
                  [
                    "id"=> 219,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Pirayú",
                  ],
                  [
                    "id"=> 220,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Quiindy",
                  ],
                  [
                    "id"=> 221,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Quyquyhó",
                  ],
                  [
                    "id"=> 222,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "San Roque González de Santa Cruz",
                  ],
                  [
                    "id"=> 223,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Sapucai",
                  ],
                  [
                    "id"=> 224,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Tebicuarymí",
                  ],
                  [
                    "id"=> 225,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Yaguarón",
                  ],
                  [
                    "id"=> 226,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Ybycuí",
                  ],
                  [
                    "id"=> 227,
                    "departamentos_id"=> 9,//PARAGUARI
                    "ciudad"=> "Ybytymí",
                  ],
                  [
                    "id"=> 228,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Benjamín Aceval",
                  ],
                  [
                    "id"=> 229,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Dr. José Falcón",
                  ],
                  [
                    "id"=> 230,
                    "departamentos_id"=> 15,
                    "ciudad"=> "General José María Bruguez",
                  ],
                  [
                    "id"=> 231,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Nanawa",
                  ],
                  [
                    "id"=> 232,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Colonia Paratodo",
                  ],
                  [
                    "id"=> 233,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Pozo Colorado",
                  ],
                  [
                    "id"=> 234,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Puerto Pinasco",
                  ],
                  [
                    "id"=> 235,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Tte. Irala Fernández",
                  ],
                  [
                    "id"=> 236,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Esteban Martínez",
                  ],
                  [
                    "id"=> 237,
                    "departamentos_id"=> 15,
                    "ciudad"=> "Villa Hayes",
                  ],
                  [
                    "id"=> 238,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Antequera",
                  ],
                  [
                    "id"=> 239,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Capiibary",
                  ],
                  [
                    "id"=> 240,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Choré",
                  ],
                  [
                    "id"=> 241,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "General Elizardo Aquino",
                  ],
                  [
                    "id"=> 242,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "General Isidoro Resquín",
                  ],
                  [
                    "id"=> 243,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Guayaibí",
                  ],
                  [
                    "id"=> 244,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Itacurubí del Rosario",
                  ],
                  [
                    "id"=> 245,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Liberación",
                  ],
                  [
                    "id"=> 246,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Lima",
                  ],
                  [
                    "id"=> 248,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Nueva Germania",
                  ],
                  [
                    "id"=> 249,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "San Estanislao",
                  ],
                  [
                    "id"=> 250,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "San Pablo",
                  ],
                  [
                    "id"=> 251,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "San Pedro de Ycuamandiyú",
                  ],
                  [
                    "id"=> 252,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "San Vicente Pancholo",
                  ],
                  [
                    "id"=> 253,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Santa Rosa del Aguaray",
                  ],
                  [
                    "id"=> 254,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Tacuatí",
                  ],
                  [
                    "id"=> 255,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Unión",
                  ],
                  [
                    "id"=> 256,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "25 de Diciembre",
                  ],
                  [
                    "id"=> 257,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Villa del Rosario",
                  ],
                  [
                    "id"=> 258,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Yataity del Norte",
                  ],
                  [
                    "id"=> 259,
                    "departamentos_id"=> 2,//SAN PEDRO
                    "ciudad"=> "Yrybucuá",
                  ]];
       
                  
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         DB::table('departamentos')->truncate();
         DB::table('ciudades')->truncate();
         foreach ($departamentos as $key => $value)
         {
             Departamento::create([
                                 'departamento' => $value['departamento'],
                                 ]);
         }

         foreach ($ciudad as $key => $value)
         {
             Ciudad::create([
                         'ciudad' => $value['ciudad'],
                         'departamentos_id' => $value['departamentos_id'],
                         ]);
         }

    }
}
