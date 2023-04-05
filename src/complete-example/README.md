# Complete Integration Guide

![tunl-embedded-final-demo](https://user-images.githubusercontent.com/2927894/230153145-2dfb6ac8-7194-4282-87df-27da687d58aa.gif)

## Overview

### Obtain API Keys

To begin, make sure you have your Tunl Account and API Keys ready.  For more info see the [Pre-Reqs](https://github.com/CKC-Technologies/tunl-embedded-payment-form#pre-reqs) Section in our main readme.

### Integration Options

This embeddable form is able to be integrated in one of 2 main ways:

- SSR Server Side Rendered (no front end library required)
- Hybrid
  - Generate an embeddable URL to use in an iframe
  - Use the `tunl-embed.js` frontend library to interact on the client
  
In this guide we will focus on the Hybrid approach.  Our [Main Readme](https://github.com/CKC-Technologies/tunl-embedded-payment-form#tunl-embeddable-form-documentation) and our basic [index.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php) has more details on the SSR technique.
