curl --location 'http://localhost:8005/api/properties' \
--header 'Accept: application/json' \
--form 'property_name="Luxury Apartment"' \
--form 'location="Dhanmondi, Dhaka"' \
--form 'rent="15000"' \
--form 'bedrooms="3"' \
--form 'bathrooms="2"' \
--form 'property_type="apartment"' \
--form 'rental_type="family"' \
--form 'description="Spacious apartment with modern amenities"' \
--form 'size="1200"' \
--form 'image=@"/path/to/property-image.jpg"'




var headers = {
  'Accept': 'application/json'
};
var request = http.MultipartRequest('POST', Uri.parse('http://localhost:8005/api/properties'));
request.fields.addAll({
  'property_name': 'Luxury Apartment',
  'location': 'Dhanmondi, Dhaka',
  'rent': '15000',
  'bedrooms': '3',
  'bathrooms': '2',
  'property_type': 'apartment',
  'rental_type': 'family',
  'description': 'Spacious apartment with modern amenities',
  'size': '1200'
});
request.files.add(await http.MultipartFile.fromPath('image', '/path/to/property-image.jpg'));
request.headers.addAll(headers);

http.StreamedResponse response = await request.send();

if (response.statusCode == 200) {
  print(await response.stream.bytesToString());
}
else {
  print(response.reasonPhrase);
}
