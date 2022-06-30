const path = require('path')

import { defineConfig } from 'vite'
import { createVuePlugin as vue } from "vite-plugin-vue2"

const partialsDirectory = path.resolve(__dirname, "./views/partials")

const svgConfig = (suffix = null) => {
    suffix = suffix !== null ? `-${suffix}` : ''

    return {
        output: {
            filename: `${partialsDirectory}/icons/icons${suffix}-svg.blade.php`,
            chunk: {
                name: `icons${suffix}`
            }
        },
        sprite: {
            prefix: 'icon--'
        },
        styles: {
            filename: `~svg-sprite-icons${suffix}.scss`,
            variables: {
                sprites: `icons${suffix}-sprites`,
                sizes: `icons${suffix}-sizes`,
                variables: `icons${suffix}-variables`,
                mixin: `icons${suffix}-sprites-mixin`
            }
        }
    }
}


const srcDirectory = 'frontend'

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue(),
    ],
    server: {
        manifest: true,
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./frontend/js"),
            "styles": path.resolve(__dirname, "./frontend/scss"),
            "fonts": path.resolve(__dirname, "./frontend/fonts"),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: '@import "styles/setup/_settings.scss";'
            },
        }
    },
    build: {
        outDir: 'dist/assets/twill',
        manifest: 'twill-manifest.json',
        rollupOptions: {
            input: {
                'main-buckets': `${srcDirectory}/js/main-buckets.js`,
                'main-dashboard': `${srcDirectory}/js/main-dashboard.js`,
                'main-form': `${srcDirectory}/js/main-form.js`,
                'main-listing': `${srcDirectory}/js/main-listing.js`,
                'main-free': `${srcDirectory}/js/main-free.js`
            }
        }
        // lib: {
        //     entry: path.resolve(__dirname, 'frontend/js/main.js'),
        //     name: 'main',
        //     fileName: (format) => `main.${format}.js`
        // }
    }
})
