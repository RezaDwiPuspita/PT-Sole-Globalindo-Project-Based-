/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],
    darkMode: "class",
    theme: {
        container: {
            center: true,
            padding: "32px",
        },
        extend: {
            colors: {
                primary: "#fef57c",
                secondary: "#fcce77",
                bg: "#f5f6fa",
                dark: "#131118",
                light: "#D9E1E1",
                grey: "#A4A1AA",
            },
            boxShadow: {
                normal: "0px 4px 40px 11px rgba(12, 34, 48, 0.06)",
            },
            aspectRatio: {
                certif: "4 / 3",
            },
        },
    },
    plugins: [require("flowbite/plugin")],
};
