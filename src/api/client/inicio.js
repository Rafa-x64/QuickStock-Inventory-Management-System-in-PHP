import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {

    const iniciar_sesion_btn = document.getElementById("iniciar_sesion");
    const registrarse_btn = document.getElementById("registrarse");

    //no eliminar (importante en caso de errores en las peticiones)
    /*fetch("api/server/index.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion: "existe_gerente" })
    })
        .then(r => r.text())
        .then(t => console.log(t));*/

    api({ accion: "existe_gerente" }).then(res => {
        const existeGerente = !!(res.existe);
        
        if (existeGerente) {
            registrarse_btn.style.display = "none";
            iniciar_sesion_btn.classList.remove("w-4");
            iniciar_sesion_btn.classList.add("w-50");
        } else {
            iniciar_sesion_btn.style.display = "none";
            registrarse_btn.classList.remove("w-4");
            registrarse_btn.classList.add("w-50");
        }
    }).catch(err => {
        console.error("Error al verificar gerente:", err);
        iniciar_sesion_btn.style.display = "inline-block";
        registrarse_btn.style.display = "inline-block";
    });

});