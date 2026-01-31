// Postcode.io lookup helper
async function lookupAddressesByPostcode(postcode) {
    const cleaned = postcode.replace(/\s+/g, '');
    const url = `https://api.postcodes.io/postcodes/${encodeURIComponent(cleaned)}`;
    try {
        const response = await fetch(url);
        if (!response.ok) return null;
        const data = await response.json();
        if (data.status !== 200 || !data.result) return null;
        return data.result;
    } catch (e) {
        return null;
    }
}

async function lookupAddressesList(postcode) {
    const cleaned = postcode.replace(/\s+/g, '');
    const url = `https://api.getaddress.io/find/${encodeURIComponent(cleaned)}?api-key=YOUR_GETADDRESS_API_KEY`;
    try {
        const response = await fetch(url);
        if (!response.ok) return null;
        const data = await response.json();
        if (!data.addresses) return null;
        return data.addresses;
    } catch (e) {
        return null;
    }
}
