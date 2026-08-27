Código procedente y heredado de proyecto previo sobre el que se han realizado cambios.

## <u>Funcionamiento previo</u>

El funcionamiento general de la aplicacion es el siguiente:

1. Autenticación: a partir de un csv presente en el proyecto. 

2. Menú: Selección del proyecto tendón, nervio longitudinal o nervio transversal. Para la cuantificación de parámetros (PHP) o para el entrenamiento de IA (Flask en diferentes puertos -> No presente en este repositorio)

3. Selección de una imagen desde un archivo .png o desde el frame seleccionado de un archivo .mp4.

4. Delimitación de 1 cm de largo en la imagen para disponer de la escala.

5. Delimitación de un rectángulo para obtener parámetros de la calidad de ese tejido.

6. Delimitación de 2 rectángulos para señalar btener parámetros de los bordes del tendón.

7. Delimitación de un rectángulo para obtener parámetros de la morfología del tendón, con cálculos diferente si la imagen es transversal o longitudinal.

8. Para el proyecto de tendón, se sigue con la delimitacion del hueso y el cálculo de sus parámetros tras la conversión de contraste y grises según un threshold.

9. Resumen de los cálculos realizados.

Todos los cálculos se guardan en un txt independiente por evaluador y por proyecto.


## <u>Funcionamiento actual</u>

1. Autenticación: a partir de una tabla en la base de datos de postgres de la aplicación. 

2. Menú: 
- Manual UZqTool 1: Contiene los proyectos anteriores: tendón, nervio longitudinal y nervio transversal, a los que se han ido añadiendo proyectos con una estructura similar. Según el proyecto hay más rectángulos y diferentes apartados, pero los cálculos son esencialmente los mimsos. A excepción de Electrolysis que ha sido programado en Flask con interfaz más similar al de UZink, aunque reproduciendo los cálculos de estos proyectos.
- UZink: los proyectos anteriormente mencionados en Flask no están en funcionamiento actualmente. En su lugar hay diferentes proyectos similares a nivel de código funcionando desde un mismo puerto. No se encuentran en este repositorio.
